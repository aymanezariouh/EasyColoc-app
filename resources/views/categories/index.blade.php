<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('colocations.show', $colocation) }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-indigo-600 transition mb-2">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                {{ $colocation->name }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Categories</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage expense categories for {{ $colocation->name }}.</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        {{-- Add Category --}}
        <x-card class="space-y-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Add Category</h2>
            </div>
            <form method="POST" action="{{ route('categories.store', $colocation) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="w-full max-w-md space-y-1.5">
                    <label for="name" class="block text-sm font-semibold text-gray-700">Category Name</label>
                    <input id="name" name="name" type="text" required placeholder="e.g. Food, Utilities, Rent..."
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition">
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-sm shadow-indigo-200 hover:shadow-md transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Create
                </button>
            </form>
        </x-card>

        {{-- Existing Categories --}}
        <x-card class="space-y-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Existing Categories</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            <th class="px-4 py-3.5">Name</th>
                            <th class="px-4 py-3.5">Update</th>
                            <th class="px-4 py-3.5">Delete</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <x-badge color="blue">{{ $category->name }}</x-badge>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <form method="POST" action="{{ route('categories.update', $category) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input name="name" type="text" value="{{ $category->name }}" required
                                               class="w-full max-w-xs rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all duration-200">
                                            <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                            Save
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3.5">
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-red-600 hover:bg-red-50 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all duration-200">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">No categories yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-app-layout>
