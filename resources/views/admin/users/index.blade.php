<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Admin Users</h1>
        <p class="text-sm text-gray-500">Ban or unban users and review account flags.</p>
    </x-slot>

    <div class="space-y-6">
        <x-card class="space-y-4">
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <th class="px-3 py-3">Name</th>
                            <th class="px-3 py-3">Email</th>
                            <th class="px-3 py-3">Admin</th>
                            <th class="px-3 py-3">Banned</th>
                            <th class="px-3 py-3">Reputation</th>
                            <th class="px-3 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-3 py-3 text-gray-700">{{ $user->email }}</td>
                                <td class="px-3 py-3">
                                    @if ($user->is_admin)
                                        <x-badge color="indigo">Admin</x-badge>
                                    @else
                                        <x-badge color="gray">User</x-badge>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    @if ($user->is_banned)
                                        <x-badge color="red">Banned</x-badge>
                                    @else
                                        <x-badge color="green">Active</x-badge>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-gray-700">{{ $user->reputation }}</td>
                                <td class="px-3 py-3">
                                    @if ($user->is_banned)
                                        <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-xs font-medium transition">
                                                Unban
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white rounded-lg px-4 py-2 text-xs font-medium transition">
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

            <div>
                {{ $users->links() }}
            </div>
        </x-card>

        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
            Back to Admin Dashboard
        </a>
    </div>
</x-app-layout>
