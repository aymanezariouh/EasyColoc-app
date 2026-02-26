<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Invitation</h1>
        <p class="mt-1 text-sm text-gray-600">Review invitation details and choose an action.</p>
    </x-slot>

    <div class="mx-auto max-w-xl">
        <x-card>
            <div class="space-y-3">
                <p class="text-sm text-gray-500">Colocation</p>
                <h2 class="text-xl font-semibold text-gray-900">{{ $invitation->colocation->name }}</h2>
                <p class="text-sm text-gray-700">Invited email: {{ $invitation->email }}</p>
                <p class="text-sm text-gray-700">Status: {{ ucfirst($invitation->status) }}</p>
                <p class="text-sm text-gray-700">Expires at: {{ $invitation->expires_at ? $invitation->expires_at->toDateTimeString() : 'No expiry' }}</p>
            </div>

            @if ($errors->has('invitation'))
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first('invitation') }}
                </div>
            @endif

            <div class="mt-6 flex flex-wrap items-center gap-2">
                @if ($invitation->status === 'pending')
                    <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            Accept
                        </button>
                    </form>

                    <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300">
                            Refuse
                        </button>
                    </form>
                @endif

                <a href="{{ route('dashboard') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300">
                    Back
                </a>
            </div>
        </x-card>
    </div>
</x-app-layout>
