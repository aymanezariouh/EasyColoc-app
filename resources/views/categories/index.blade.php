<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Categories</h1>
        <p class="text-sm text-gray-500">Manage expense categories for {{ $colocation->name }}.</p>
    </x-slot>

    <div class="space-y-6">
        <x-card class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Add Category</h2>
            <form method="POST" action="{{ route('categories.store', $colocation) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="w-full max-w-md space-y-1">
                    <label for="name" class="text-sm font-medium text-gray-700">Category Name</label>
                    <input id="name" name="name" type="text" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit"
                        class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                    Create
                </button>
            </form>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Existing Categories</h2>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <th class="px-3 py-3">Name</th>
                            <th class="px-3 py-3">Update</th>
                            <th class="px-3 py-3">Delete</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 font-medium text-gray-900">{{ $category->name }}</td>
                                <td class="px-3 py-3">
                                    <form method="POST" action="{{ route('categories.update', $category) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input name="name" type="text" value="{{ $category->name }}" required
                                               class="w-full max-w-xs rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="submit"
                                                class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-3 py-1.5 text-xs font-medium transition">
                                            Save
                                        </button>
                                    </form>
                                </td>
                                <td class="px-3 py-3">
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-1.5 text-xs font-medium transition">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-4 text-sm text-gray-500">No categories yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <a href="{{ route('colocations.show', $colocation) }}"
           class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
            Back to Colocation
        </a>
    </div>
</x-app-layout>
