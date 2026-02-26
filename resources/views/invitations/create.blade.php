<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('colocations.show', $colocation) }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; {{ $colocation->name }}</a>
            <h1 class="mt-1 text-2xl font-semibold text-gray-900">Invite Member</h1>
            <p class="mt-1 text-sm text-gray-600">Send an invitation by email.</p>
        </div>
    </x-slot>

    <div class="max-w-xl">
        <x-card>
            <form method="POST" action="{{ route('invitations.store', $colocation) }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Send Invitation
                    </button>
                    <a href="{{ route('colocations.show', $colocation) }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300">
                        Back
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
