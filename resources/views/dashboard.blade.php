<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Overview of your active colocation and invitations.</p>
    </x-slot>

    <div class="space-y-6">
        @if ($activeMembership)
            <x-card>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Your active colocation</p>
                        <h2 class="mt-1 text-xl font-semibold text-gray-900">{{ $activeMembership->colocation->name }}</h2>
                        <p class="mt-1 text-sm text-gray-600">Role: {{ ucfirst($activeMembership->role) }}</p>
                    </div>
                    <a href="{{ route('colocations.show', $activeMembership->colocation) }}"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Open Colocation
                    </a>
                </div>
            </x-card>
        @else
            <x-card>
                <div class="text-center">
                    <p class="text-sm text-gray-500">No active colocation yet.</p>
                    <h2 class="mt-2 text-2xl font-semibold text-gray-900">Create your first colocation</h2>
                    <p class="mt-2 text-sm text-gray-600">Start by creating a shared space and inviting your roommates.</p>
                    <a href="{{ route('colocations.create') }}"
                        class="mt-6 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Create Colocation
                    </a>
                </div>
            </x-card>
        @endif

        <x-card>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Pending Invitations</h2>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-700">{{ $pendingInvitations->count() }} pending</span>
            </div>

            @if ($pendingInvitations->isEmpty())
                <p class="text-sm text-gray-600">No pending invitations.</p>
            @else
                <ul class="space-y-2">
                    @foreach ($pendingInvitations as $invitation)
                        <li>
                            <a href="{{ route('invitations.show', $invitation->token) }}"
                                class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
                                <span class="font-medium text-gray-900">{{ $invitation->colocation->name }}</span>
                                <span class="text-sm text-gray-600">Review</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        @if (auth()->user()?->is_admin)
            <x-card>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Admin</h2>
                        <p class="text-sm text-gray-600">Access platform stats and user moderation tools.</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300">
                        Open Admin Dashboard
                    </a>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>
