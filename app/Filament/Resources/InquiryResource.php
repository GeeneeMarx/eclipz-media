<?php

namespace App\Filament\Resources;

use App\Enums\InquiryStatus;
use App\Filament\Resources\InquiryResource\Pages;
use App\Models\AdminActivityLog;
use App\Models\Inquiry;
use App\Services\ActivityLogger;
use App\Services\InquiryNotifier;
use Carbon\CarbonInterface;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?string $recordTitleAttribute = 'reference';

    // Enquiries arrive through the public form only. There is no create page in admin.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        $statusOptions = collect(InquiryStatus::cases())
            ->mapWithKeys(fn (InquiryStatus $status) => [$status->value => $status->label()])
            ->all();

        return $form->schema([
            Forms\Components\Section::make('Inquiry ID')
                ->schema([
                    Forms\Components\Placeholder::make('reference_display')
                        ->hiddenLabel()
                        ->content(fn ($record) => static::referenceHtml($record)),
                ]),

            Forms\Components\Section::make('Visitor')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')->disabled(),
                    Forms\Components\TextInput::make('company')->disabled(),
                    Forms\Components\TextInput::make('email')->disabled(),
                    Forms\Components\TextInput::make('phone')->label('Phone / WhatsApp')->disabled(),
                ]),

            Forms\Components\Section::make('Enquiry')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('service_requested')->label('Service')->disabled(),
                    Forms\Components\Placeholder::make('budget_display')
                        ->label('Budget')
                        ->content(fn ($record) => $record?->budgetLabel() ?? 'Not given'),
                    Forms\Components\Textarea::make('message')->rows(6)->disabled()->columnSpanFull(),
                    Forms\Components\Placeholder::make('submitted_display')
                        ->label('Submitted')
                        ->content(fn ($record) => $record?->submittedAtLabel() ?? '-'),
                    Forms\Components\Placeholder::make('updated_display')
                        ->label('Last updated')
                        ->content(fn ($record) => $record?->updated_at
                            ? $record->updated_at->copy()->setTimezone(config('inquiry.timezone'))->format('j F Y, g:i A T')
                            : '-'),
                ]),

            Forms\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options($statusOptions)
                        ->required()
                        ->helperText('Moving an enquiry forward to Under Review, Responded or Closed emails the visitor once. Saving without a change, or moving back to an earlier stage, sends nothing.'),
                ]),

            Forms\Components\Section::make('Email delivery')
                ->description('"Accepted" means the mail server took the message. It does not prove the message reached an inbox.')
                ->schema([
                    Forms\Components\Placeholder::make('admin_delivery')
                        ->label('Admin notification')
                        ->content(fn ($record) => static::deliveryLine($record?->admin_notified_at, $record?->admin_notification_error)),
                    Forms\Components\Placeholder::make('visitor_delivery')
                        ->label('Visitor confirmation')
                        ->content(fn ($record) => static::deliveryLine($record?->visitor_confirmed_at, $record?->visitor_confirmation_error)),
                    Forms\Components\Placeholder::make('status_delivery')
                        ->label('Status update to visitor')
                        ->content(fn ($record) => static::statusDeliveryLine($record)),
                ]),

            Forms\Components\Section::make('Recent admin activity')
                ->schema([
                    Forms\Components\Placeholder::make('activity')
                        ->hiddenLabel()
                        ->content(fn ($record) => static::activityContent($record)),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $statusOptions = collect(InquiryStatus::cases())
            ->mapWithKeys(fn (InquiryStatus $status) => [$status->value => $status->label()])
            ->all();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Inquiry ID')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Inquiry ID copied'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('j M Y, g:i A', config('inquiry.timezone'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Visitor')->searchable(),
                Tables\Columns\TextColumn::make('company')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('service_requested')->label('Service')->searchable(),
                Tables\Columns\TextColumn::make('budget')
                    ->formatStateUsing(fn (?string $state) => $state ? config("inquiry.budget_options.{$state}", $state) : null)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (InquiryStatus $state) => $state->color())
                    ->formatStateUsing(fn (InquiryStatus $state) => $state->label()),
                Tables\Columns\TextColumn::make('delivery')
                    ->label('Emails')
                    ->state(fn (Inquiry $record) => $record->deliverySummary())
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Failed' => 'danger',
                        'Sent' => 'success',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options($statusOptions),
                Tables\Filters\SelectFilter::make('service_requested')
                    ->label('Service')
                    ->options(fn () => Inquiry::query()
                        ->whereNotNull('service_requested')
                        ->distinct()
                        ->orderBy('service_requested')
                        ->pluck('service_requested', 'service_requested')
                        ->all()),
                Tables\Filters\Filter::make('submitted')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Submitted from'),
                        Forms\Components\DatePicker::make('until')->label('Submitted until'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
                Tables\Filters\Filter::make('failed_emails')
                    ->label('Email problems')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where(function (Builder $q) {
                        $q->whereNotNull('admin_notification_error')
                            ->orWhereNotNull('visitor_confirmation_error')
                            ->orWhereNotNull('status_notification_error');
                    })),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Review'),
                Tables\Actions\Action::make('resendEmails')
                    ->label('Resend failed emails')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Inquiry $record) => $record->hasFailedEmails())
                    ->requiresConfirmation()
                    ->modalHeading('Resend failed emails?')
                    ->modalDescription(fn (Inquiry $record) => 'This retries only the emails that failed ('.static::kindNames($record->failedEmailKinds()).'). Emails the mail server already accepted are never sent again.')
                    ->action(fn (Inquiry $record) => static::resendFailedEmails($record)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInquiries::route('/'),
            'edit' => Pages\EditInquiry::route('/{record}/edit'),
        ];
    }

    /** Retries only the failed emails and tells the admin what happened. Used by the list and the edit page. */
    public static function resendFailedEmails(Inquiry $record): void
    {
        $result = app(InquiryNotifier::class)->resendFailed($record);

        if ($result['busy']) {
            Notification::make()
                ->title('A resend is already running for this enquiry')
                ->body('Please wait a moment and check the delivery status again.')
                ->warning()
                ->send();

            return;
        }

        ActivityLogger::log('resent_emails', 'Inquiry', $record->id, "Retried failed emails for enquiry {$record->reference}", [
            'attempted' => $result['attempted'],
            'sent' => $result['sent'],
            'still_failing' => $result['failed'],
        ]);

        if ($result['attempted'] === []) {
            Notification::make()->title('Nothing to resend')->body('No failed emails are recorded for this enquiry.')->info()->send();
        } elseif ($result['failed'] === []) {
            Notification::make()
                ->title('Accepted by the mail server')
                ->body(static::kindNames($result['sent']).'. Acceptance does not prove the message reached an inbox.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Some emails still failed')
                ->body('Still failing: '.static::kindNames($result['failed']).'. See the Email delivery section for the reason.')
                ->danger()
                ->send();
        }
    }

    public static function kindNames(array $kinds): string
    {
        return implode(', ', array_map(fn (string $kind) => Inquiry::EMAIL_LABELS[$kind] ?? $kind, $kinds));
    }

    public static function referenceHtml(?Inquiry $record): HtmlString|string
    {
        if (! $record) {
            return '-';
        }

        $reference = e($record->reference);
        $js = Js::from($record->reference);

        // Reuses the clipboard helper registered by the admin panel (it copies any text it is given).
        return new HtmlString(<<<HTML
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                <span style="font-size:1.5rem;font-weight:700;letter-spacing:0.08em;">{$reference}</span>
                <button type="button" onclick="window.eclipzCopyProjectUrl(this, {$js})" style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.875rem;font-weight:500;color:rgb(var(--primary-600));cursor:pointer;">
                    <span data-copy-label>Copy ID</span>
                </button>
            </div>
        HTML);
    }

    public static function deliveryLine(?CarbonInterface $acceptedAt, ?string $error): string
    {
        if (filled($error)) {
            return 'Failed: '.$error;
        }

        if ($acceptedAt) {
            return 'Accepted by the mail server on '.$acceptedAt->copy()->setTimezone(config('inquiry.timezone'))->format('j M Y, g:i A');
        }

        return 'Not recorded';
    }

    public static function statusDeliveryLine(?Inquiry $record): string
    {
        if (! $record) {
            return '-';
        }

        if (filled($record->status_notification_error)) {
            return 'Failed: '.$record->status_notification_error;
        }

        if ($record->status_notified_at) {
            $stage = InquiryStatus::tryFrom((string) $record->last_notified_status)?->label() ?? 'update';

            return 'Accepted by the mail server on '.$record->status_notified_at->copy()->setTimezone(config('inquiry.timezone'))->format('j M Y, g:i A').' ('.$stage.')';
        }

        return 'No status email sent';
    }

    public static function activityContent(?Inquiry $record): HtmlString|string
    {
        if (! $record) {
            return '-';
        }

        $entries = AdminActivityLog::query()
            ->where('entity_type', 'Inquiry')
            ->where('entity_id', $record->getKey())
            ->with('user')
            ->latest('created_at')
            ->limit(10)
            ->get();

        if ($entries->isEmpty()) {
            return 'No admin activity has been recorded for this enquiry yet.';
        }

        return new HtmlString($entries->map(function (AdminActivityLog $entry) {
            $who = $entry->user?->name ?? 'System';
            $when = $entry->created_at?->copy()->setTimezone(config('inquiry.timezone'))->format('j M Y, g:i A');

            return '<div style="margin-bottom:0.4rem;">'.e($who.': '.$entry->description).' <span style="opacity:0.6;">('.e((string) $when).')</span></div>';
        })->implode(''));
    }
}