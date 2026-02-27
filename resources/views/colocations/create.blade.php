<x-app-layout>
    <x-slot name="header">
        <h1 class="ui-title">Create Colocation</h1>
        <p class="ui-subtitle">Start a new shared space for your group.</p>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <x-card>
            <form method="POST" action="{{ route('colocations.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="field-label">Colocation Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required class="field-input" placeholder="e.g. Casa Verde">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn-primary">Create</button>
                    <a href="{{ route('dashboard') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
