<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaLibraryItemResource\Pages;
use App\Models\MediaLibraryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaLibraryItemResource extends Resource
{
    protected static ?string $model = MediaLibraryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Media Library';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('path')
                ->label('File')
                ->directory('media-library')
                ->required()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if ($state) {
                        $set('mime_type', $state->getMimeType());
                        $set('size_bytes', $state->getSize());
                    }
                })
                ->live(),
            Forms\Components\TextInput::make('label'),
            Forms\Components\Hidden::make('mime_type'),
            Forms\Components\Hidden::make('size_bytes'),
            Forms\Components\Hidden::make('uploaded_by')->default(fn () => Auth::id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')->label('Preview'),
                Tables\Columns\TextColumn::make('label'),
                Tables\Columns\TextColumn::make('mime_type'),
                Tables\Columns\TextColumn::make('size_bytes')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 1).' KB' : '—'),
                Tables\Columns\TextColumn::make('uploader.name')->label('Uploaded by'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(fn (MediaLibraryItem $record) => Storage::disk('public')->delete($record->path)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaLibraryItems::route('/'),
            'create' => Pages\CreateMediaLibraryItem::route('/create'),
            'edit' => Pages\EditMediaLibraryItem::route('/{record}/edit'),
        ];
    }
}