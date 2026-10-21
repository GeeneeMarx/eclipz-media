<?php

namespace App\Support;

use Filament\Actions\MountableAction;
use Filament\Forms\Components\Field;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Column;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Route;

/**
 * Attaches the wording in config/admin-help.php and config/admin-help-fields.php to the admin
 * panel: a short line and an info icon on every described page and field, and tooltips on
 * buttons and table columns. Anything without an entry is left exactly as it was.
 */
class AdminHelp
{
    public static function register(): void
    {
        // Every form field: the short line below it and the info icon beside its label.
        Field::configureUsing(function (Field $field): void {
            $field
                ->helperText(fn (Field $component, mixed $livewire) => static::field($livewire, $component->getName())['short'] ?? null)
                ->hintIcon(
                    fn (Field $component, mixed $livewire) => static::field($livewire, $component->getName()) ? 'heroicon-o-information-circle' : null,
                    fn (Field $component, mixed $livewire) => static::field($livewire, $component->getName())['full'] ?? null,
                );
        });

        // Every button (a tooltip set in code on a specific button wins over these).
        MountableAction::configureUsing(function (MountableAction $action): void {
            $action->tooltip(fn (MountableAction $action) => static::lookup('actions', $action->getName()));
        });

        // Every table column.
        Column::configureUsing(function (Column $column): void {
            $column->tooltip(fn (Column $column) => static::lookup('columns', $column->getName()));
        });

        // The line and info icon under each page heading.
        $hook = defined(PanelsRenderHook::class.'::CONTENT_START')
            ? PanelsRenderHook::CONTENT_START
            : PanelsRenderHook::PAGE_START;

        FilamentView::registerRenderHook(
            $hook,
            fn (): string => ($help = static::page()) ? view('filament.help.page-intro', ['help' => $help])->render() : ''
        );
    }

    /** One part of the help wording, merged from both config files. */
    public static function section(string $key): array
    {
        return array_replace_recursive(
            (array) config("admin-help.{$key}", []),
            (array) config("admin-help-fields.{$key}", [])
        );
    }

    /** Help for one field, found by the resource or page it is on and the field name. */
    public static function field(mixed $livewire, ?string $name): ?array
    {
        $scope = static::scopeFor($livewire);

        if ($scope === null || $name === null) {
            return null;
        }

        return (static::section('fields')[$scope] ?? [])[$name] ?? null;
    }

    public static function lookup(string $group, ?string $name): ?string
    {
        return $name === null ? null : (static::section($group)[$name] ?? null);
    }

    /** Help for the page being shown, found by its route name. */
    public static function page(): ?array
    {
        $name = (string) Route::currentRouteName();

        if (! str_starts_with($name, 'filament.admin.')) {
            return null;
        }

        $key = substr($name, strlen('filament.admin.'));
        $pages = static::section('pages');

        return $pages[$key] ?? ($pages[preg_replace('/\.[^.]+$/', '.*', $key)] ?? null);
    }

    private static function scopeFor(mixed $livewire): ?string
    {
        if (! is_object($livewire)) {
            return null;
        }

        // Resource pages are described under the resource, standalone pages under themselves.
        return method_exists($livewire, 'getResource')
            ? class_basename($livewire::getResource())
            : class_basename($livewire);
    }
}