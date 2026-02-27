<x-app-layout>
    <x-slot name="header">
        <h1 class="ui-title">Dashboard</h1>
        <p class="ui-subtitle">Overview of your active colocation and pending invitations.</p>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    @if ($activeMembership)
                        <div>
                            <p class="text-sm font-medium text-slate-500">Active colocation</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ $activeMembership->colocation->name }}</h2>
                            <p class="mt-2 text-sm text-slate-600">Role: <span class="font-medium">{{ ucfirst($activeMembership->role) }}</span></p>
                        </div>

                        <a href="{{ route('colocations.show', $activeMembership->colocation) }}" class="btn-primary">
                            Open Colocation
                        </a>
                    @else
                        <div>
                            <p class="text-sm font-medium text-slate-500">No active colocation</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-900">Create your first colocation</h2>
                            <p class="mt-2 text-sm text-slate-600">Create a shared space to start tracking expenses with your roommates.</p>
                        </div>

                        <a href="{{ route('colocations.create') }}" class="btn-primary">
                            Create Colocation
                        </a>
                    @endif
                </div>
            </x-card>

            <x-card>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Pending Invitations</h2>
                    <x-badge color="blue">{{ $pendingInvitations->count() }} pending</x-badge>
                </div>

                @if ($pendingInvitations->isEmpty())
                    <p class="text-sm text-slate-600">No pending invitations.</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($pendingInvitations as $invitation)
                            <li>
                                <a href="{{ route('invitations.show', $invitation->token) }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $invitation->colocation->name }}</p>
                                        <p class="text-xs text-slate-500">Invited email: {{ $invitation->email }}</p>
                                    </div>
                                    <span class="text-sm text-indigo-600">Open</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card>
                <h2 class="text-lg font-semibold text-slate-900">Quick Tips</h2>
                <ul class="mt-4 space-y-2 text-sm text-slate-600">
                    <li>- Keep categories simple and clear.</li>
                    <li>- Log expenses as soon as they happen.</li>
                    <li>- Use "Mark Paid" to keep balances accurate.</li>
                </ul>
            </x-card>

            @if (auth()->user()?->is_admin)
                <x-card>
                    <h2 class="text-lg font-semibold text-slate-900">Admin Access</h2>
                    <p class="mt-2 text-sm text-slate-600">Manage users, bans, and platform metrics.</p>
                    <a href="{{ route('admin.dashboard') }}" class="btn-secondary mt-4 w-full">
                        Open Admin Dashboard
                    </a>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
