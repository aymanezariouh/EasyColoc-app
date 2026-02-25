<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-indigo-600 transition mb-2">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                Dashboard
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Invitation</h1>
            <p class="text-sm text-gray-500 mt-0.5">Review invitation details and choose an action.</p>
        </div>
    </x-slot>

    <div class="max-w-lg mx-auto">
        <x-card class="space-y-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Colocation</p>
                    </div>
                    @if ($invitation->status === 'pending')
                        <x-badge color="amber">Pending</x-badge>
                    @elseif ($invitation->status === 'accepted')
                        <x-badge color="green">Accepted</x-badge>
                    @else
                        <x-badge color="gray">Refused</x-badge>
                    @endif
                </div>
                <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                    <p class="text-lg font-bold text-gray-900">{{ $invitation->colocation->name }}</p>
                    <p class="text-sm text-gray-600">
                        <span class="text-gray-400">Invited email:</span> {{ $invitation->email }}
                    </p>
                    <p class="text-xs text-gray-400">
                        Expires at: {{ $invitation->expires_at ? $invitation->expires_at->toDateTimeString() : 'No expiry' }}
                    </p>
                </div>
            </div>

            @if ($errors->has('invitation'))
                <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    {{ $errors->first('invitation') }}
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-3">
                <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-sm shadow-indigo-200 hover:shadow-md transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        Accept
                    </button>
                </form>

                <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-200">
                        Refuse
                    </button>
                </form>

                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-700 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-200">
                    Back
                </a>
            </div>
        </x-card>
    </div>
</x-app-layout>
