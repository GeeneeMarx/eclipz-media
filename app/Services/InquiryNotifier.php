<?php

namespace App\Services;

use App\Enums\InquiryStatus;
use App\Mail\InquiryReceivedMail;
use App\Mail\InquiryStatusUpdateMail;
use App\Mail\NewInquiryAdminMail;
use App\Models\ContactSetting;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Sends the emails about an enquiry. Each email is attempted on its own, its
 * outcome is stored on the enquiry, and nothing here can throw: a mail problem
 * must never undo or hide a saved enquiry or a saved status.
 *
 * "Accepted" means the mail server accepted the message. It does not prove the
 * message reached an inbox.
 */
class InquiryNotifier
{
    private const KINDS = [
        'admin' => ['at' => 'admin_notified_at', 'error' => 'admin_notification_error', 'label' => 'admin notification'],
        'visitor' => ['at' => 'visitor_confirmed_at', 'error' => 'visitor_confirmation_error', 'label' => 'visitor confirmation'],
    ];

    public function notifyNewInquiry(Inquiry $inquiry): void
    {
        // An email the mail server already accepted is never sent again.
        if ($inquiry->admin_notified_at === null) {
            $this->sendAdminNotification($inquiry);
        }

        if ($inquiry->visitor_confirmed_at === null) {
            $this->sendVisitorConfirmation($inquiry);
        }
    }

    /**
     * Emails the visitor when an enquiry moves FORWARD to a stage they have
     * not been told about. Same status, a move backward, or New: nothing.
     */
    public function notifyStatusChange(Inquiry $inquiry): void
    {
        $status = $inquiry->status;

        if (! $status instanceof InquiryStatus || ! $status->notifiesVisitor()) {
            return;
        }

        if ($status->rank() <= $this->lastNotifiedRank($inquiry)) {
            return;
        }

        $this->sendStatusUpdate($inquiry, $status);
    }

    /**
     * Retries only the emails that have a recorded failure. Emails the mail
     * server already accepted are never sent again, and a lock stops two
     * resends of the same enquiry from running at once.
     *
     * @return array{busy: bool, attempted: array, sent: array, failed: array}
     */
    public function resendFailed(Inquiry $inquiry): array
    {
        $lock = Cache::lock('inquiry-resend:'.$inquiry->getKey(), 60);

        if (! $lock->get()) {
            return ['busy' => true, 'attempted' => [], 'sent' => [], 'failed' => []];
        }

        try {
            $inquiry->refresh();
            $attempted = $inquiry->failedEmailKinds();

            if (in_array('admin', $attempted, true) && $inquiry->admin_notified_at === null) {
                $this->sendAdminNotification($inquiry);
            }

            if (in_array('visitor', $attempted, true) && $inquiry->visitor_confirmed_at === null) {
                $this->sendVisitorConfirmation($inquiry);
            }

            if (in_array('status', $attempted, true)) {
                $status = $inquiry->status;

                if ($status->notifiesVisitor() && $status->rank() > $this->lastNotifiedRank($inquiry)) {
                    $this->sendStatusUpdate($inquiry, $status);
                } else {
                    // Nothing is owed to the visitor any more (for example the status moved back).
                    $this->save($inquiry, ['status_notification_error' => null]);
                }
            }

            $inquiry->refresh();
            $failed = $inquiry->failedEmailKinds();

            return [
                'busy' => false,
                'attempted' => $attempted,
                'sent' => array_values(array_diff($attempted, $failed)),
                'failed' => $failed,
            ];
        } finally {
            $lock->release();
        }
    }

    private function lastNotifiedRank(Inquiry $inquiry): int
    {
        $last = filled($inquiry->last_notified_status)
            ? InquiryStatus::tryFrom($inquiry->last_notified_status)
            : null;

        return $last ? $last->rank() : 0;
    }

    private function sendAdminNotification(Inquiry $inquiry): void
    {
        try {
            $recipient = ContactSetting::current()->inquiry_notification_email;

            if (blank($recipient)) {
                $this->recordFailure($inquiry, 'admin', 'No inquiry notification email is configured.');

                return;
            }

            Mail::to($recipient)->send(new NewInquiryAdminMail($inquiry));

            $this->recordSuccess($inquiry, 'admin');
        } catch (Throwable $e) {
            $this->recordFailure($inquiry, 'admin', $this->summarise($e), $e);
        }
    }

    private function sendVisitorConfirmation(Inquiry $inquiry): void
    {
        try {
            Mail::to($inquiry->email)->send(new InquiryReceivedMail($inquiry));

            $this->recordSuccess($inquiry, 'visitor');
        } catch (Throwable $e) {
            $this->recordFailure($inquiry, 'visitor', $this->summarise($e), $e);
        }
    }

    private function sendStatusUpdate(Inquiry $inquiry, InquiryStatus $status): void
    {
        try {
            Mail::to($inquiry->email)->send(new InquiryStatusUpdateMail($inquiry, $status));

            $this->save($inquiry, [
                'last_notified_status' => $status->value,
                'status_notified_at' => now(),
                'status_notification_error' => null,
            ]);
        } catch (Throwable $e) {
            $summary = $this->summarise($e);

            // last_notified_status stays as it was, so the email is still owed and can be resent.
            $this->save($inquiry, ['status_notification_error' => mb_substr($summary, 0, 255)]);

            Log::warning('Inquiry email failed', [
                'reference' => $inquiry->reference,
                'email' => 'status update ('.$status->value.')',
                'summary' => $summary,
                'detail' => $this->maskedDetail($e),
            ]);
        }
    }

    private function recordSuccess(Inquiry $inquiry, string $kind): void
    {
        $columns = self::KINDS[$kind];

        $this->save($inquiry, [$columns['at'] => now(), $columns['error'] => null]);
    }

    private function recordFailure(Inquiry $inquiry, string $kind, string $summary, ?Throwable $e = null): void
    {
        $columns = self::KINDS[$kind];

        $this->save($inquiry, [$columns['at'] => null, $columns['error'] => mb_substr($summary, 0, 255)]);

        Log::warning('Inquiry email failed', [
            'reference' => $inquiry->reference,
            'email' => $columns['label'],
            'summary' => $summary,
            'detail' => $e ? $this->maskedDetail($e) : null,
        ]);
    }

    /**
     * Writes only the delivery-tracking columns, straight to the table, and
     * keeps the in-memory model in step. It must NOT save the model: this runs
     * from inside the model's "updated" event, where the status still counts as
     * changed, so a model save would re-fire the event and re-send the email in
     * a loop whenever sending fails. It also leaves "last updated" alone, because
     * an email outcome is not an edit.
     */
    private function save(Inquiry $inquiry, array $attributes): void
    {
        try {
            DB::table($inquiry->getTable())
                ->where($inquiry->getKeyName(), $inquiry->getKey())
                ->update($attributes);

            $inquiry->forceFill($attributes)->syncOriginalAttributes(array_keys($attributes));
        } catch (Throwable $e) {
            Log::error('Could not record inquiry email outcome', [
                'reference' => $inquiry->reference,
                'exception' => get_class($e),
            ]);
        }
    }

    /** Short, non-sensitive description stored on the enquiry, for example "TransportException, SMTP 535, authentication failed". */
    private function summarise(Throwable $e): string
    {
        $message = $e->getMessage();
        $parts = [class_basename($e)];

        if (preg_match('/got code "(\d{3})"/', $message, $match)) {
            $parts[] = "SMTP {$match[1]}";
        }

        if (str_contains($message, 'Failed to authenticate')) {
            $parts[] = 'authentication failed';
        } elseif (str_contains($message, 'Connection could not be established')) {
            $parts[] = 'connection failed';
        } elseif (stripos($message, 'timed out') !== false) {
            $parts[] = 'timed out';
        }

        return implode(', ', $parts);
    }

    /** The server's own wording is useful for diagnosis; email addresses in it are masked. */
    private function maskedDetail(Throwable $e): string
    {
        $masked = preg_replace('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', '[email]', $e->getMessage());

        return mb_substr((string) $masked, 0, 300);
    }
}