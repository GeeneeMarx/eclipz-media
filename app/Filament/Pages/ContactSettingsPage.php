<?php

namespace App\Filament\Pages;

use App\Models\ContactSetting;
use App\Services\ActivityLogger;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * One save here propagates to every public page that displays contact
 * details, since they all read from ContactSetting::current() rather than
 * hardcoded values.
 */
class ContactSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Contact Information';

    protected static string $view = 'filament.pages.contact-settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(ContactSetting::current()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->columns(2)->schema([
            Forms\Components\TextInput::make('phone')->tel(),
            Forms\Components\TextInput::make('whatsapp')
                ->tel()
                ->helperText('Use the international format: country code first, no leading 0 (for example +234 then the number). The website uses this for the WhatsApp follow-up button. Leave it empty until the number is confirmed.'),
            Forms\Components\TextInput::make('email')
                ->label('Public email')
                ->email()
                ->helperText('Shown on the website and used as the reply address on emails to visitors. Use the general public address.'),
            Forms\Components\TextInput::make('inquiry_notification_email')
                ->label('Inquiry notification email')
                ->email()
                ->helperText('Private. New enquiries are emailed here. It is never shown on the public website and is separate from the address that sends the emails.'),
            Forms\Components\TextInput::make('address'),
            Forms\Components\TextInput::make('instagram')->url(),
            Forms\Components\TextInput::make('facebook')->url(),
            Forms\Components\TextInput::make('linkedin')->url(),
            Forms\Components\TextInput::make('tiktok')->url(),
        ])->statePath('data');
    }

    public function save(): void
    {
        ContactSetting::current()->update($this->form->getState());
        ActivityLogger::log('updated', 'ContactSetting', null, 'Updated company contact information');
        Notification::make()->title('Saved')->success()->send();
    }
}