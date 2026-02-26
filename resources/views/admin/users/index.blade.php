<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">User Management</h1>
        <p class="mt-1 text-sm text-gray-600">Ban or unban users.</p>
    </x-slot>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Admin</th>
                        <th class="px-3 py-2">Banned</th>
                        <th class="px-3 py-2">Reputation</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $user->email }}</td>
                            <td class="px-3 py-2">{{ $user->is_admin ? 'Yes' : 'No' }}</td>
                            <td class="px-3 py-2">{{ $user->is_banned ? 'Yes' : 'No' }}</td>
                            <td class="px-3 py-2">{{ $user->reputation }}</td>
                            <td class="px-3 py-2">
                                @if ($user->is_banned)
                                    <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-gray-200 px-3 py-1.5 text-xs font-medium text-gray-800 hover:bg-gray-300">
                                            Unban
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">
                                            Ban
                                        </button>
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
