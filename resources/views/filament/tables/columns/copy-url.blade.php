<div class="px-3 py-2">
    <button
        type="button"
        onclick="event.preventDefault(); event.stopPropagation(); window.eclipzCopyProjectUrl(this, @js($getRecord()->publicUrl()))"
        class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
        <span data-copy-label>Copy URL</span>
    </button>
</div>