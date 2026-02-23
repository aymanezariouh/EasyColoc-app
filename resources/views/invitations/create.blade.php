<x-app-layout>
    <h1>Invite Member</h1>
    <p>Colocation: {{ $colocation->name }}</p>

    @if ($errors->any())
        <p>{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('invitations.store', $colocation) }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>
        <button type="submit">Send Invitation</button>
    </form>
</x-app-layout>
