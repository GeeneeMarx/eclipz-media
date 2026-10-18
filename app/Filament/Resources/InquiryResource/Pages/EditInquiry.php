<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Enums\InquiryStatus;
use App\Filament\Resources\InquiryResource;
use App\Services\ActivityLogger;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditInquiry extends EditRecord
{
    protected static string $resource = InquiryResource::class;

    protected ?InquiryStatus $statusBeforeSave = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resendEmails')
                ->label('Resend failed emails')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => $this->record->hasFailedEmails())
                ->requiresConfirmation()
                ->modalHeading('Resend failed emails?')
                ->modalDescription('Only emails that failed are retried. Emails the mail server already accepted are never sent again.')
                ->action(function () {
                    InquiryResource::resendFailedEmails($this->record);
                    $this->record->refresh();
                    $this->fillForm();
                }),
        ];
    }

    protected function beforeSave(): void
    {
        $this->statusBeforeSave = $this->record->status;
    }

    protected function afterSave(): void
    {
        $record = $this->record->refresh();
        $before = $this->statusBeforeSave;
        $after = $record->status;

        // The status is the only editable field. No change means nothing to log or email.
        if ($before === $after) {
            return;
        }

        ActivityLogger::log(
            'status_changed',
            'Inquiry',
            $record->id,
            "Changed status of enquiry {$record->reference} from {$before?->label()} to {$after->label()}",
            ['from' => $before?->value, 'to' => $after->value]
        );

        if (! $after->notifiesVisitor()) {
            return;
        }

        if (filled($record->status_notification_error)) {
            Notification::make()
                ->title('Status saved, but the visitor email failed')
                ->body('Reason: '.$record->status_notification_error.'. Use "Resend failed emails" to try again.')
                ->warning()
                ->send();
        } elseif ($record->last_notified_status === $after->value && $record->status_notified_at?->gt(now()->subMinute())) {
            Notification::make()
                ->title('Visitor emailed')
                ->body('The mail server accepted the status update. That does not prove it reached the inbox.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Status saved')
                ->body('The visitor was not emailed because this is not a step forward from what they were last told.')
                ->info()
                ->send();
        }
    }
}