<x-app-layout>
    <h1>Invitation</h1>
    <p>Colocation: {{ $invitation->colocation->name }}</p>
    <p>Invited email: {{ $invitation->email }}</p>
    <p>Status: {{ $invitation->status }}</p>
    <p>Expires at: {{ $invitation->expires_at ? $invitation->expires_at->toDateTimeString() : 'No expiry' }}</p>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @if ($errors->has('invitation'))
        <p>{{ $errors->first('invitation') }}</p>
    @endif

    <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
        @csrf
        <button type="submit">Accept</button>
    </form>

    <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
        @csrf
        <button type="submit">Refuse</button>
    </form>
</x-app-layout>
