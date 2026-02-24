<x-app-layout>
    <h1>Admin Users</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <p>{{ $errors->first() }}</p>
    @endif

    <table border="1" cellpadding="4" cellspacing="0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Is Admin</th>
                <th>Is Banned</th>
                <th>Reputation</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->is_admin ? 'yes' : 'no' }}</td>
                    <td>{{ $user->is_banned ? 'yes' : 'no' }}</td>
                    <td>{{ $user->reputation }}</td>
                    <td>
                        @if ($user->is_banned)
                            <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                @csrf
                                <button type="submit">Unban</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                                @csrf
                                <button type="submit">Ban</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        {{ $users->links() }}
    </div>

    <p>
        <a href="{{ route('admin.dashboard') }}">Back to dashboard</a>
    </p>
</x-app-layout>
