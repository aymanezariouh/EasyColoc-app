<x-app-layout>
    <x-slot name="header">
        <h1 class="ui-title">Invitation</h1>
        <p class="ui-subtitle">Review details and choose your action.</p>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <x-card>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm text-slate-500">Colocation</p>
                <h2 class="mt-1 text-xl font-semibold text-slate-900">{{ $invitation->colocation->name }}</h2>
                <p class="mt-2 text-sm text-slate-700">Invited email: {{ $invitation->email }}</p>
                <p class="mt-1 text-sm text-slate-700">Status: {{ ucfirst($invitation->status) }}</p>
                <p class="mt-1 text-sm text-slate-700">Expires at: {{ $invitation->expires_at ? $invitation->expires_at->toDateTimeString() : 'No expiry' }}</p>
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
                        <button type="submit" class="btn-primary">Accept</button>
                    </form>

                    <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
                        @csrf
                        <button type="submit" class="btn-secondary">Refuse</button>
                    </form>
                @endif

                <a href="{{ route('dashboard') }}" class="btn-ghost">Back</a>
            </div>
        </x-card>
    </div>
</x-app-layout>
