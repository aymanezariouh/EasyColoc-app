<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Create Colocation</h1>
        <p class="text-sm text-gray-500">Start a new shared space.</p>
    </x-slot>

    <div class="max-w-xl">
        <x-card class="space-y-5">
            <form method="POST" action="{{ route('colocations.store') }}" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="name" class="text-sm font-medium text-gray-700">Colocation Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                        Create
                    </button>
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                        Cancel
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
