<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('colocations.show', $colocation) }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-indigo-600 transition mb-2">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                {{ $colocation->name }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Invite Member</h1>
            <p class="text-sm text-gray-500 mt-0.5">Send an email invitation to join {{ $colocation->name }}.</p>
        </div>
    </x-slot>

    <div class="max-w-lg mx-auto">
        <x-card class="space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Colocation</p>
                    <p class="text-base font-bold text-gray-900">{{ $colocation->name }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('invitations.store', $colocation) }}" class="space-y-5">
                @csrf
                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-semibold text-gray-700">Email Address</label>
                    <input id="email" name="email" type="email" required placeholder="colleague@example.com"
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-sm shadow-indigo-200 hover:shadow-md transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                        Send Invitation
                    </button>
                    <a href="{{ route('colocations.show', $colocation) }}"
                       class="inline-flex items-center bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-200">
                        Back
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
