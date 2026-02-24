<x-app-layout>
    <x-slot name="header">
        <h2>Invite Member</h2>
    </x-slot>

    <p>Colocation: {{ $colocation->name }}</p>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <p>{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('invitations.store', $colocation) }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>
        <button type="submit">Send Invitation</button>
    </form>

    <p>
        <a href="{{ route('colocations.show', $colocation) }}">Back to colocation</a>
    </p>
</x-app-layout>
