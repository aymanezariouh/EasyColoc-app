<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-indigo-600 transition mb-2">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    Admin Dashboard
                </a>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">User Management</h1>
                <p class="text-sm text-gray-500 mt-0.5">Ban or unban users and review account flags.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-card class="space-y-5">
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            <th class="px-4 py-3.5">User</th>
                            <th class="px-4 py-3.5">Role</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5">Reputation</th>
                            <th class="px-4 py-3.5">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-500 text-xs font-bold text-white flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($user->is_admin)
                                        <x-badge color="indigo">Admin</x-badge>
                                    @else
                                        <x-badge color="gray">User</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($user->is_banned)
                                        <x-badge color="red">Banned</x-badge>
                                    @else
                                        <x-badge color="green">Active</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    @php
                                        $rep = (int) $user->reputation;
                                        $repColor = $rep > 0 ? 'text-emerald-600' : ($rep < 0 ? 'text-red-600' : 'text-gray-500');
                                    @endphp
                                    <span class="font-semibold {{ $repColor }}">{{ $user->reputation }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($user->is_banned)
                                        <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 text-gray-700 rounded-lg px-3.5 py-2 text-xs font-semibold transition-all duration-200">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                                Unban
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 text-red-600 hover:bg-red-50 rounded-lg px-3.5 py-2 text-xs font-semibold transition-all duration-200">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
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

            <div class="pt-2">
                {{ $users->links() }}
            </div>
        </x-card>
    </div>
</x-app-layout>
