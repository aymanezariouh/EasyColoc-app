<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500">Overview of your active colocation and invitations.</p>
    </x-slot>

    <div class="space-y-6">
        @if ($activeMembership)
            <x-card class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active Colocation</p>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $activeMembership->colocation->name }}</h2>
                    </div>
                    <x-badge :color="$activeMembership->role === 'owner' ? 'indigo' : 'gray'">
                        {{ ucfirst($activeMembership->role) }}
                    </x-badge>
                </div>

                <a href="{{ route('colocations.show', $activeMembership->colocation) }}"
                   class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                    Open Colocation
                </a>
            </x-card>
        @else
            <x-card class="text-center py-12">
                <p class="text-sm font-medium text-gray-500">No active colocation yet.</p>
                <h2 class="mt-2 text-xl font-semibold text-gray-900">Create your first colocation</h2>
                <p class="mt-2 text-sm text-gray-500">Start by creating a shared space and inviting your roommates.</p>
                <div class="mt-6">
                    @if (Route::has('colocations.create'))
                        <a href="{{ route('colocations.create') }}"
                           class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                            Create Colocation
                        </a>
                    @else
                        <button type="button" disabled
                                class="inline-flex items-center bg-gray-200 text-gray-500 rounded-lg px-4 py-2 text-sm font-medium cursor-not-allowed">
                            Create Colocation
                        </button>
                    @endif
                </div>
            </x-card>
        @endif

        <x-card class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Pending Invitations</h2>
                <x-badge color="gray">{{ $pendingInvitations->count() }} pending</x-badge>
            </div>

            <div class="space-y-3">
                @forelse ($pendingInvitations as $invitation)
                    <div class="flex flex-wrap items-center justify-between rounded-lg border border-gray-200 px-4 py-3 gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $invitation->colocation->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $invitation->email }}
                                @if ($invitation->expires_at)
                                    · expires {{ $invitation->expires_at->toDateString() }}
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('invitations.show', $invitation->token) }}"
                           class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                            Open
                        </a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No pending invitations.</p>
                @endforelse
            </div>
        </x-card>

        @if (auth()->user()->is_admin)
            <x-card class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Admin Area</h2>
                    <p class="text-sm text-gray-500">Review platform stats and manage users.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                    Open Admin Dashboard
                </a>
            </x-card>
        @endif
    </div>
</x-app-layout>
