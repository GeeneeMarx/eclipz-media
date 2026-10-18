<?php

namespace App\Filament\Pages;

use App\Models\SeoSetting;
use App\Services\ActivityLogger;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SeoSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'SEO Defaults';

    protected static string $view = 'filament.pages.seo-settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SeoSetting::current()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('default_meta_title'),
            Forms\Components\Textarea::make('default_meta_description'),
            Forms\Components\FileUpload::make('default_og_image')->image()->directory('seo'),
        ])->statePath('data');
    }

    public function save(): void
    {
        SeoSetting::current()->update($this->form->getState());
        ActivityLogger::log('updated', 'SeoSetting', null, 'Updated default SEO settings');
        Notification::make()->title('Saved')->success()->send();
    }
}
