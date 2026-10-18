<?php

namespace App\Filament\Resources;

use App\Enums\ProjectType;
use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use App\Services\ActivityLogger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Portfolio';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Project details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        }),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('This controls the project\'s public URL (/work/this-slug). Changing it on a published project changes its live link.'),
                    Forms\Components\Select::make('industry_id')
                        ->label('Industry')
                        ->relationship('industry', 'name')
                        ->searchable(),
                    Forms\Components\Select::make('client_partner_id')
                        ->label('Client')
                        ->relationship('client', 'name')
                        ->searchable()
                        ->preload()
                        ->helperText('Optional - links this project to an existing client/partner record.'),
                    Forms\Components\Select::make('project_type')
                        ->label('Project Type')
                        ->options(collect(ProjectType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                        ->default(ProjectType::Visual->value)
                        ->required()
                        ->live(),
                    Forms\Components\TextInput::make('location'),
                    Forms\Components\TextInput::make('year')->numeric(),
                    Forms\Components\TextInput::make('short_description')
                        ->maxLength(160)
                        ->helperText('Shown on portfolio listing cards and used as the page summary. Keep it to one sentence.')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->label('Full description')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Media')
                ->columns(2)
                ->schema([
                    Forms\Components\FileUpload::make('cover_image')
                        ->image()
                        ->directory('projects/covers')
                        ->maxSize(5120),
                    Forms\Components\TextInput::make('video_url')
                        ->label('Video URL')
                        ->url()
                        ->helperText('YouTube or Vimeo link embeds automatically. A direct .mp4 link plays inline. Anything else shows as a plain link.'),
                    Forms\Components\TextInput::make('project_url')
                        ->label('Live Project URL')
                        ->url()
                        ->visible(fn (Forms\Get $get) => $get('project_type') === ProjectType::Digital->value)
                        ->helperText('For Digital Experience projects - link to the live website/app, if approved to share. Separate from this project\'s own Eclipz portfolio page URL, which is generated automatically from its slug.'),
                    Forms\Components\Repeater::make('images')
                        ->relationship('images')
                        ->schema([
                            Forms\Components\FileUpload::make('path')
                                ->image()
                                ->directory('projects/gallery')
                                ->maxSize(5120)
                                ->required(),
                            Forms\Components\TextInput::make('alt_text')
                                ->helperText('Describe what the image shows - used for accessibility, not just a filename.'),
                        ])
                        ->columns(2)
                        ->reorderable()
                        ->orderColumn('sort_order')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Services provided')
                ->schema([
                    Forms\Components\Select::make('services')
                        ->relationship('services', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload(),
                ]),

            Forms\Components\Section::make('Case study details')
                ->visible(fn (Forms\Get $get) => $get('project_type') === ProjectType::CaseStudy->value)
                ->relationship('caseStudyDetail')
                ->schema([
                    Forms\Components\Textarea::make('challenge'),
                    Forms\Components\Textarea::make('strategy'),
                    Forms\Components\Textarea::make('execution'),
                ]),

            Forms\Components\Section::make('Results / metrics')
                ->schema([
                    Forms\Components\Repeater::make('metrics')
                        ->relationship('metrics')
                        ->schema([
                            Forms\Components\TextInput::make('label')->required(),
                            Forms\Components\TextInput::make('value')->required(),
                        ])
                        ->columns(2)
                        ->helperText('Only add metrics actually supplied by Eclipz - never estimate or invent a number.'),
                ]),

            Forms\Components\Section::make('Visibility')
                ->columns(3)
                ->schema([
                    Forms\Components\Toggle::make('is_published')
                        ->helperText('Only published projects are visible on the public site.'),
                    Forms\Components\Toggle::make('is_featured')
                        ->helperText('Featured projects can appear in homepage/portfolio highlights.'),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->default('-'),
                Tables\Columns\TextColumn::make('industry.name')->label('Industry'),
                Tables\Columns\TextColumn::make('project_type')->label('Type')->badge(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
                // A Blade view column (not ->html()), so the button's onclick
                // handler is rendered as written. The click script itself is
                // registered in AdminPanelProvider.
                Tables\Columns\ViewColumn::make('copy_url')
                    ->label('')
                    ->view('filament.tables.columns.copy-url'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('industry_id')->label('Industry')->relationship('industry', 'name'),
                Tables\Filters\SelectFilter::make('client_partner_id')->label('Client')->relationship('client', 'name'),
                Tables\Filters\SelectFilter::make('project_type')
                    ->label('Type')
                    ->options(collect(ProjectType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
                Tables\Filters\TernaryFilter::make('is_published'),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Tables\Actions\Action::make('togglePublish')
                    ->label(fn (Project $record) => $record->is_published ? 'Unpublish' : 'Publish')
                    ->icon(fn (Project $record) => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->action(function (Project $record) {
                        $nowPublishing = ! $record->is_published;
                        $record->update([
                            'is_published' => $nowPublishing,
                            'published_at' => $nowPublishing && ! $record->published_at ? now() : $record->published_at,
                        ]);
                        ActivityLogger::log(
                            $nowPublishing ? 'published' : 'unpublished',
                            'Project',
                            $record->id,
                            ($nowPublishing ? 'Published' : 'Unpublished')." project \"{$record->name}\""
                        );
                    }),
                Tables\Actions\Action::make('toggleFeature')
                    ->label(fn (Project $record) => $record->is_featured ? 'Unfeature' : 'Feature')
                    ->icon(fn (Project $record) => $record->is_featured ? 'heroicon-o-star' : 'heroicon-o-star')
                    ->action(function (Project $record) {
                        $record->update(['is_featured' => ! $record->is_featured]);
                        ActivityLogger::log(
                            $record->is_featured ? 'featured' : 'unfeatured',
                            'Project',
                            $record->id,
                            ($record->is_featured ? 'Featured' : 'Unfeatured')." project \"{$record->name}\""
                        );
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(fn (Project $record) => ActivityLogger::log('deleted', 'Project', $record->id, "Deleted project \"{$record->name}\"")),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}