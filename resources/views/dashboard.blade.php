<x-app-layout>
    <x-slot name="header">
        <h2>Dashboard</h2>
    </x-slot>

    <div>
        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif

        <h3>Active Colocation</h3>
        @if ($activeMembership)
            <p>
                <a href="{{ route('colocations.show', $activeMembership->colocation) }}">
                    Open {{ $activeMembership->colocation->name }}
                </a>
            </p>
            <p>Your role: {{ $activeMembership->role }}</p>
        @else
            <p>You do not have an active colocation.</p>
            @if (Route::has('colocations.create'))
                <p>
                    <a href="{{ route('colocations.create') }}">Create Colocation</a>
                </p>
            @else
                <p>
                    <button type="button" disabled>Create Colocation</button>
                </p>
            @endif
        @endif

        <h3>Pending Invitations</h3>
        <ul>
            @forelse ($pendingInvitations as $invitation)
                <li>
                    {{ $invitation->colocation->name }}
                    @if ($invitation->expires_at)
                        (expires {{ $invitation->expires_at->toDateString() }})
                    @endif
                    <a href="{{ route('invitations.show', $invitation->token) }}">Open invitation</a>
                </li>
            @empty
                <li>No pending invitations.</li>
            @endforelse
        </ul>

        @if (auth()->user()->is_admin)
            <h3>Admin</h3>
            <p>
                <a href="{{ route('admin.dashboard') }}">Open admin dashboard</a>
            </p>
        @endif
    </div>
</x-app-layout>
