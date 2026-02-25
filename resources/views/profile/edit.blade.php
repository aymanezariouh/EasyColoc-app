<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-indigo-600 transition mb-2">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                Dashboard
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ __('Profile') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage your account settings and preferences.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-card>

        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-card>

        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-card>
    </div>
</x-app-layout>
