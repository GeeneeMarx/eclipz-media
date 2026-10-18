<?php

namespace App\Filament\Pages;

use App\Models\AdminActivityLog;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

/**
 * Read-only, by design (see §4 of the brief). No edit/delete actions are
 * registered anywhere on this page — this is an audit trail, not editable
 * content.
 */
class ActivityLogPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Activity Log';

    protected static string $view = 'filament.pages.activity-log-page';

    public function table(Table $table): Table
    {
        return $table
            ->query(AdminActivityLog::query()->latest('created_at'))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Admin')->default('System'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('entity_type')->badge(),
                Tables\Columns\TextColumn::make('ip_address'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }
}
