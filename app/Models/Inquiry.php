<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use App\Services\InquiryNotifier;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use RuntimeException;

class Inquiry extends Model
{
    // No 0/O/1/I so an ID read out loud or typed on a phone is not misread.
    private const REFERENCE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    private const REFERENCE_ATTEMPTS = 5;

    /** Names shown to admins for each email the system sends about an enquiry. */
    public const EMAIL_LABELS = [
        'admin' => 'Admin notification',
        'visitor' => 'Visitor confirmation',
        'status' => 'Status update to visitor',
    ];

    // 'reference', 'submission_token' and the delivery-tracking columns are
    // deliberately NOT fillable, so none can be set from submitted form data.
    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'service_requested',
        'budget',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => InquiryStatus::class,
            'admin_notified_at' => 'datetime',
            'visitor_confirmed_at' => 'datetime',
            'status_notified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Inquiry $inquiry) {
            if (blank($inquiry->reference)) {
                $inquiry->reference = static::generateReference();
            }
        });

        // The Inquiry ID is permanent: any attempt to change it on update is undone.
        static::updating(function (Inquiry $inquiry) {
            if ($inquiry->isDirty('reference')) {
                $inquiry->reference = $inquiry->getOriginal('reference');
            }
        });

        // Only a genuine change of status can trigger a visitor email. Saves that
        // leave the status alone, and the notifier's own bookkeeping saves, never do.
        static::updated(function (Inquiry $inquiry) {
            if ($inquiry->wasChanged('status')) {
                app(InquiryNotifier::class)->notifyStatusChange($inquiry);
            }
        });
    }

    /**
     * EZ-YYYY-XXXXXX. The year uses the business timezone (config/inquiry.php)
     * and the suffix comes from a cryptographically secure generator.
     */
    public static function generateReference(?CarbonInterface $at = null): string
    {
        $year = ($at ?? now())->copy()->setTimezone(config('inquiry.timezone', 'Africa/Lagos'))->format('Y');

        $suffix = '';
        $max = strlen(self::REFERENCE_ALPHABET) - 1;

        for ($i = 0; $i < 6; $i++) {
            $suffix .= self::REFERENCE_ALPHABET[random_int(0, $max)];
        }

        return "EZ-{$year}-{$suffix}";
    }

    /**
     * Saves a new enquiry from validated form data. The database unique
     * index on `reference` is the real guarantee; if the (extremely unlikely)
     * collision happens, a new reference is generated and the save retried.
     * A collision on the submission token is rethrown for the caller to handle.
     */
    public static function createFromSubmission(array $data, string $submissionToken): self
    {
        for ($attempt = 1; $attempt <= self::REFERENCE_ATTEMPTS; $attempt++) {
            $inquiry = new static($data);
            $inquiry->status = InquiryStatus::New;
            $inquiry->submission_token = $submissionToken;
            $inquiry->reference = static::generateReference();

            try {
                $inquiry->save();

                return $inquiry;
            } catch (UniqueConstraintViolationException $e) {
                if (! static::isReferenceCollision($e) || $attempt === self::REFERENCE_ATTEMPTS) {
                    throw $e;
                }
            }
        }

        throw new RuntimeException('Could not generate a unique inquiry reference.');
    }

    /**
     * The exception message also contains the SQL, which lists the
     * `reference` column even when the duplicate was the submission token.
     * So match the unique index name that the database reports instead.
     */
    private static function isReferenceCollision(UniqueConstraintViolationException $e): bool
    {
        $message = $e->getMessage();

        return str_contains($message, 'inquiries_reference_unique')
            || str_contains($message, 'inquiries.reference');
    }

    public static function followUpMessage(string $reference): string
    {
        return "Hello Eclipz Media, I have submitted an enquiry on your website. My Inquiry ID is {$reference}. I would like to follow up on my request.";
    }

    public function budgetLabel(): ?string
    {
        if (blank($this->budget)) {
            return null;
        }

        return config("inquiry.budget_options.{$this->budget}", $this->budget);
    }

    /** Submission time in the business timezone, for example "11 October 2026, 3:42 PM WAT". */
    public function submittedAtLabel(): string
    {
        $time = $this->created_at ?? now();

        return $time->copy()
            ->setTimezone(config('inquiry.timezone', 'Africa/Lagos'))
            ->format('j F Y, g:i A T');
    }

    /** Which emails currently have a recorded failure: any of 'admin', 'visitor', 'status'. */
    public function failedEmailKinds(): array
    {
        return array_keys(array_filter([
            'admin' => filled($this->admin_notification_error),
            'visitor' => filled($this->visitor_confirmation_error),
            'status' => filled($this->status_notification_error),
        ]));
    }

    public function hasFailedEmails(): bool
    {
        return $this->failedEmailKinds() !== [];
    }

    /**
     * "Failed", "Sent" (accepted by the mail server, not proof of inbox
     * delivery) or "Not recorded" (older enquiries and enquiries with no attempt).
     */
    public function deliverySummary(): string
    {
        if ($this->hasFailedEmails()) {
            return 'Failed';
        }

        if ($this->admin_notified_at && $this->visitor_confirmed_at) {
            return 'Sent';
        }

        return 'Not recorded';
    }
}