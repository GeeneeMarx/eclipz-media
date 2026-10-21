<div class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400" role="note">
    <p class="flex-1">{{ $help['short'] }}</p>

    @if(filled($help['full'] ?? null))
        <button
            type="button"
            x-data
            x-tooltip="{ content: @js($help['full']), theme: $store.theme }"
            aria-label="More information about this page"
            class="shrink-0 rounded text-gray-400 transition hover:text-primary-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
        >
            <x-filament::icon icon="heroicon-o-information-circle" class="h-5 w-5" />
        </button>
    @endif
</div>