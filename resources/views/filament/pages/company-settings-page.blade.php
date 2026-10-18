<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="[
                \Filament\Actions\Action::make('save')->submit('save')->label('Save changes'),
            ]"
        />
    </form>
</x-filament-panels::page>