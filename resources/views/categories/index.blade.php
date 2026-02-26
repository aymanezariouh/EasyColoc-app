<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('colocations.show', $colocation) }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; {{ $colocation->name }}</a>
            <h1 class="mt-1 text-2xl font-semibold text-gray-900">Categories</h1>
            <p class="mt-1 text-sm text-gray-600">Manage expense categories for this colocation.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-card>
            <h2 class="text-lg font-semibold text-gray-900">Add Category</h2>
            <form method="POST" action="{{ route('categories.store', $colocation) }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <div class="w-full max-w-md">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input id="name" name="name" type="text" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Create
                </button>
            </form>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-gray-900">Existing Categories</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="px-3 py-2">Name</th>
                            <th class="px-3 py-2">Update</th>
                            <th class="px-3 py-2">Delete</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $category->name }}</td>
                                <td class="px-3 py-2">
                                    <form method="POST" action="{{ route('categories.update', $category) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input name="name" type="text" value="{{ $category->name }}" required
                                            class="w-full max-w-xs rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="submit" class="rounded-lg bg-gray-200 px-3 py-1.5 text-xs font-medium text-gray-800 hover:bg-gray-300">
                                            Save
                                        </button>
                                    </form>
                                </td>
                                <td class="px-3 py-2">
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-4 text-center text-gray-500">No categories yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-app-layout>
