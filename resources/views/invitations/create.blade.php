<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Invite Member</h1>
        <p class="text-sm text-gray-500">Send an email invitation to join {{ $colocation->name }}.</p>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <x-card class="space-y-5">
            <div class="space-y-1">
                <p class="text-sm font-medium text-gray-700">Colocation</p>
                <p class="text-base font-semibold text-gray-900">{{ $colocation->name }}</p>
            </div>

            <form method="POST" action="{{ route('invitations.store', $colocation) }}" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                        Send Invitation
                    </button>
                    <a href="{{ route('colocations.show', $colocation) }}"
                       class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                        Back
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
