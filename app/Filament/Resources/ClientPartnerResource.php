<?php

namespace App\Filament\Resources;

use App\Enums\ClientPartnerType;
use App\Filament\Resources\ClientPartnerResource\Pages;
use App\Models\ClientPartner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientPartnerResource extends Resource
{
    protected static ?string $model = ClientPartner::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Portfolio';

    protected static ?string $navigationLabel = 'Clients & Partners';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('type')
                ->options(collect(ClientPartnerType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                ->default(ClientPartnerType::Client->value)
                ->required(),
            Forms\Components\Select::make('industry_id')->relationship('industry', 'name')->searchable(),
            Forms\Components\TextInput::make('website')->url(),

            Forms\Components\FileUpload::make('logo')
                // Not ->image(): that turns on the browser-side image preview, which hung on
                // "Waiting for sizes" for some files. The accepted types are checked here
                // and again on the server (see AppServiceProvider). SVG is deliberately not
                // accepted: an SVG can contain script.
                ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                ->directory('clients/logos')
                ->maxSize(2048)
                ->helperText('Upload the original logo as PNG, JPG or WebP (SVG is not accepted). The website fits it into a consistent display area without cropping or distortion, so there is no need to resize it to match other logos first.'),

            Forms\Components\TextInput::make('display_scale')
                ->label('Display scale')
                ->numeric()
                ->default(1.00)
                ->step(0.05)
                ->minValue(0.5)
                ->maxValue(2.0)
                ->helperText('Leave at 1.00 for normal size. Only adjust if this specific logo looks visually smaller or larger than the others once on the live site. This never crops or resizes the uploaded file itself.'),

            Forms\Components\Toggle::make('is_visible')
                ->helperText('Keep off until a real logo has been uploaded. Never publish a placeholder as if it were the client\'s brand.'),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('industry.name'),
                Tables\Columns\IconColumn::make('is_visible')->boolean(),
            ])
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientPartners::route('/'),
            'create' => Pages\CreateClientPartner::route('/create'),
            'edit' => Pages\EditClientPartner::route('/{record}/edit'),
        ];
    }
}