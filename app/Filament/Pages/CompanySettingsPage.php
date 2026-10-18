<?php

namespace App\Filament\Pages;

use App\Models\CompanySetting;
use App\Services\ActivityLogger;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CompanySettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Company';

    protected static string $view = 'filament.pages.company-settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(CompanySetting::current()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Company')
                ->schema([
                    Forms\Components\TextInput::make('company_name')->required(),
                    Forms\Components\TextInput::make('tagline'),
                    Forms\Components\TextInput::make('short_description')
                        ->helperText('Shown as the intro line on the About page and used as its meta description.'),
                ]),
            Forms\Components\Section::make('About Content')
                ->schema([
                    Forms\Components\Textarea::make('story')->label('How We Started')->rows(4),
                    Forms\Components\Textarea::make('what_we_do')->rows(4),
                    Forms\Components\Textarea::make('differentiator')->label('What Makes Eclipz Different')->rows(4),
                    Forms\Components\Textarea::make('client_type_text')->label('The Type of Clients We Work With')->rows(3),
                ]),
            Forms\Components\Section::make('Mission & Vision')
                ->schema([
                    Forms\Components\Textarea::make('mission'),
                    Forms\Components\Textarea::make('vision'),
                ]),
            Forms\Components\Section::make('Founder')
                ->schema([
                    Forms\Components\TextInput::make('founder_name'),
                    Forms\Components\TextInput::make('founder_title'),
                    Forms\Components\Textarea::make('founder_bio'),
                    Forms\Components\TextInput::make('founder_specializations')
                        ->helperText('Comma-separated, e.g. "Hospitality, Real estate, Travel"'),
                    Forms\Components\TextInput::make('founder_quote'),
                ]),
            Forms\Components\FileUpload::make('logo')->image()->directory('brand'),
            Forms\Components\FileUpload::make('favicon')->image()->directory('brand'),
        ])->statePath('data');
    }

    public function save(): void
    {
        CompanySetting::current()->update($this->form->getState());
        ActivityLogger::log('updated', 'CompanySetting', null, 'Updated company settings');
        Notification::make()->title('Saved')->success()->send();
    }
}