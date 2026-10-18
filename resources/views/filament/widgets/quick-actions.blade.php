<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Quick actions</x-slot>

        @if(count($actions))
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0.75rem;">
                @foreach($actions as $action)
                    <div style="display:flex;flex-direction:column;gap:0.35rem;">
                        <x-filament::button tag="a" :href="$action['url']" color="gray" outlined>
                            {{ $action['label'] }}
                        </x-filament::button>
                        <span style="font-size:0.75rem;color:rgb(var(--gray-500));">{{ $action['description'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="font-size:0.875rem;color:rgb(var(--gray-500));">No quick actions are available for your account.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>