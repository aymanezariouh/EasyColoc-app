<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('colocations.show', $colocation) }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; {{ $colocation->name }}</a>
            <h1 class="mt-1 text-2xl font-semibold text-slate-900">Invite Member</h1>
            <p class="mt-1 text-sm text-slate-600">Send a secure invitation by email.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <x-card>
            <form method="POST" action="{{ route('invitations.store', $colocation) }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="field-label">Email</label>
                    <input id="email" name="email" type="email" required class="field-input" placeholder="member@example.com">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn-primary">Send Invitation</button>
                    <a href="{{ route('colocations.show', $colocation) }}" class="btn-secondary">Back</a>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
