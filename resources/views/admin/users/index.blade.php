<x-app-layout>
    <x-slot name="header">
        <h1 class="ui-title">User Management</h1>
        <p class="ui-subtitle">Moderate user access and account status.</p>
    </x-slot>

    <x-card>
        <div class="ui-table-wrap">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th>Banned</th>
                        <th>Reputation</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->is_admin)
                                    <x-badge color="indigo">Yes</x-badge>
                                @else
                                    <x-badge color="gray">No</x-badge>
                                @endif
                            </td>
                            <td>
                                @if ($user->is_banned)
                                    <x-badge color="red">Yes</x-badge>
                                @else
                                    <x-badge color="green">No</x-badge>
                                @endif
                            </td>
                            <td>{{ $user->reputation }}</td>
                            <td>
                                @if ($user->is_banned)
                                    <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                        @csrf
                                        <button type="submit" class="btn-secondary btn-sm">Unban</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                                        @csrf
                                        <button type="submit" class="btn-danger btn-sm">Ban</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </x-card>
</x-app-layout>
