<x-app-layout>
    <h1>{{ $colocation->name }}</h1>
    <p>Status: {{ $colocation->status }}</p>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @if ($membership && $membership->role !== 'owner')
        <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
            @csrf
            <button type="submit">Leave Colocation</button>
        </form>
    @endif

    @if ($membership && $membership->role === 'owner')
        <form method="POST" action="{{ route('colocations.cancel', $colocation) }}">
            @csrf
            <button type="submit">Cancel Colocation</button>
        </form>
    @endif
</x-app-layout>
