<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Invitation</h1>
        <p class="text-sm text-gray-500">Review invitation details and choose an action.</p>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <x-card class="space-y-5">
            <div class="space-y-2">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">Colocation</p>
                    @if ($invitation->status === 'pending')
                        <x-badge color="indigo">Pending</x-badge>
                    @elseif ($invitation->status === 'accepted')
                        <x-badge color="green">Accepted</x-badge>
                    @else
                        <x-badge color="gray">Refused</x-badge>
                    @endif
                </div>
                <p class="text-lg font-semibold text-gray-900">{{ $invitation->colocation->name }}</p>
                <p class="text-sm text-gray-600">Invited email: {{ $invitation->email }}</p>
                <p class="text-xs text-gray-500">
                    Expires at: {{ $invitation->expires_at ? $invitation->expires_at->toDateTimeString() : 'No expiry' }}
                </p>
            </div>

            @if ($errors->has('invitation'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first('invitation') }}
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-2">
                <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                        Accept
                    </button>
                </form>

                <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                        Refuse
                    </button>
                </form>

                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                    Back
                </a>
            </div>
        </x-card>
    </div>
</x-app-layout>
