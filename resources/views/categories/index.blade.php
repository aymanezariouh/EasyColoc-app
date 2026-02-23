<x-app-layout>
    <h1>Categories for {{ $colocation->name }}</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <p>{{ $errors->first() }}</p>
    @endif

    <h2>Add Category</h2>
    <form method="POST" action="{{ route('categories.store', $colocation) }}">
        @csrf
        <label for="name">Name</label>
        <input id="name" name="name" type="text" required>
        <button type="submit">Create</button>
    </form>

    <h2>Existing Categories</h2>
    <table border="1" cellpadding="4" cellspacing="0">
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
                    <td>{{ $category->name }}</td>
                    <td>
                        <form method="POST" action="{{ route('categories.update', $category) }}">
                            @csrf
                            @method('PUT')
                            <input name="name" type="text" value="{{ $category->name }}" required>
                            <button type="submit">Save</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No categories yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p>
        <a href="{{ route('colocations.show', $colocation) }}">Back to colocation</a>
    </p>
</x-app-layout>
