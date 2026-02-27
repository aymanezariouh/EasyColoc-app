<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('colocations.show', $colocation) }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; {{ $colocation->name }}</a>
            <h1 class="mt-1 text-2xl font-semibold text-slate-900">Categories</h1>
            <p class="mt-1 text-sm text-slate-600">Manage expense categories for this colocation.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-card>
            <h2 class="text-lg font-semibold text-slate-900">Add Category</h2>
            <form method="POST" action="{{ route('categories.store', $colocation) }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <div class="w-full max-w-md">
                    <label for="name" class="field-label">Name</label>
                    <input id="name" name="name" type="text" required class="field-input" placeholder="e.g. Food">
                </div>
                <button type="submit" class="btn-primary">Create</button>
            </form>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-slate-900">Existing Categories</h2>

            <div class="mt-4 ui-table-wrap">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Update</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="font-medium text-slate-900">{{ $category->name }}</td>
                                <td>
                                    <form method="POST" action="{{ route('categories.update', $category) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input name="name" type="text" value="{{ $category->name }}" required class="field-input !mt-0 max-w-xs">
                                        <button type="submit" class="btn-secondary btn-sm">Save</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-slate-500">No categories yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-app-layout>
