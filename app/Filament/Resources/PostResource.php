<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'blog-posts';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Blog posts';

    protected static ?string $modelLabel = 'blog post';

    protected static ?string $pluralModelLabel = 'blog posts';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\Section::make('Article')->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(160)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation): void {
                                    // Suggest the web address from the title, only for a new post.
                                    if ($operation === 'create' && blank($get('slug'))) {
                                        $set('slug', Str::slug((string) $state));
                                    }
                                }),

                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->maxLength(160)
                                ->unique(ignoreRecord: true)
                                ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                                ->validationMessages(['regex' => 'Use lowercase letters, numbers and single hyphens only.'])
                                ->helperText('The web address of the post: /blog/your-address. Changing it after publishing breaks links that were already shared.'),

                            Forms\Components\Textarea::make('excerpt')
                                ->rows(3)
                                ->maxLength(300)
                                ->helperText('A short summary shown on the blog page and in search and social previews.'),

                            Forms\Components\RichEditor::make('body')
                                ->label('Article')
                                ->required()
                                ->columnSpanFull()
                                ->toolbarButtons([
                                    'h2', 'h3', 'bold', 'italic', 'link',
                                    'bulletList', 'orderedList', 'blockquote',
                                    'undo', 'redo',
                                ]),
                        ]),
                    ]),

                Forms\Components\Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Section::make('Publishing')->schema([
                            Forms\Components\Select::make('user_id')
                                ->label('Author')
                                ->relationship('author', 'name')
                                ->default(fn () => auth()->id())
                                ->required(),

                            Forms\Components\TextInput::make('category')
                                ->maxLength(80)
                                ->datalist(fn (): array => Post::query()
                                    ->whereNotNull('category')
                                    ->distinct()
                                    ->orderBy('category')
                                    ->pluck('category')
                                    ->all())
                                ->helperText('Optional. Pick an existing one or type a new one.'),

                            Forms\Components\Toggle::make('is_published')
                                ->label('Published')
                                ->default(false)
                                ->helperText('Off keeps it a draft that only you can preview.'),

                            Forms\Components\DateTimePicker::make('published_at')
                                ->label('Publish date')
                                ->timezone(config('inquiry.timezone'))
                                ->seconds(false)
                                ->helperText('Leave empty to publish as soon as Published is on. A future date schedules the post.'),
                        ]),

                        Forms\Components\Section::make('Cover image')->schema([
                            Forms\Components\FileUpload::make('cover_image')
                                ->label('Cover image')
                                ->disk('public')
                                ->directory('blog/covers')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(2048)
                                ->helperText('JPG, PNG or WebP, up to 2 MB. A landscape image about 1600 px wide works best.'),

                            Forms\Components\TextInput::make('cover_alt')
                                ->label('Image description')
                                ->maxLength(200)
                                ->helperText('A short description for screen readers and search. Leave empty to use the title.'),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->disk('public')
                    ->square()
                    ->size(48),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->description(fn (Post $record): ?string => $record->category),
                Tables\Columns\TextColumn::make('author.name')->label('Author'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->getStateUsing(fn (Post $record): string => $record->statusLabel())
                    ->color(fn (string $state): string => match ($state) {
                        'Published' => 'success',
                        'Scheduled' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publish date')
                    ->dateTime('j M Y, g:i A', config('inquiry.timezone'))
                    ->placeholder('Not set')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('open')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Post $record): string => route('blog.show', $record->slug), shouldOpenInNewTab: true),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}