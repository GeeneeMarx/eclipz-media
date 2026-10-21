@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4 font-body text-sm">
        <div class="min-w-[7rem]">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center border border-jet/15 px-5 py-2.5 text-jet/40" aria-disabled="true">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center border border-jet px-5 py-2.5 transition-colors hover:bg-jet hover:text-white">Previous</a>
            @endif
        </div>

        <p class="sm:hidden text-jet/60">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</p>

        <ul class="hidden sm:flex items-center gap-2">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li aria-hidden="true" class="px-2 text-jet/60">{{ $element }}</li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span aria-current="page" class="inline-flex h-10 w-10 items-center justify-center bg-jet text-white">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" aria-label="Go to page {{ $page }}" class="inline-flex h-10 w-10 items-center justify-center border border-jet/20 transition-colors hover:border-jet">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </ul>

        <div class="min-w-[7rem] text-right">
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center border border-jet px-5 py-2.5 transition-colors hover:bg-jet hover:text-white">Next</a>
            @else
                <span class="inline-flex items-center border border-jet/15 px-5 py-2.5 text-jet/40" aria-disabled="true">Next</span>
            @endif
        </div>
    </nav>
@endif