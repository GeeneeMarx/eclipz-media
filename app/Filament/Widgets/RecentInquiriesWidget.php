<?php

namespace App\Filament\Widgets;

use App\Enums\InquiryStatus;
use App\Filament\Resources\InquiryResource;
use App\Models\Inquiry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentInquiriesWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent enquiries';

    public function table(Table $table): Table
    {
        return $table
            ->query(Inquiry::query()->latest('id')->limit(8))
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Inquiry ID')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Inquiry ID copied'),
                Tables\Columns\TextColumn::make('name')->label('Visitor'),
                Tables\Columns\TextColumn::make('service_requested')->label('Service'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (InquiryStatus $state) => $state->color())
                    ->formatStateUsing(fn (InquiryStatus $state) => $state->label()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('j M Y, g:i A', config('inquiry.timezone')),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Inquiry $record) => InquiryResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading('No enquiries yet')
            ->emptyStateDescription('New enquiries from the website contact form will appear here.');
    }
}