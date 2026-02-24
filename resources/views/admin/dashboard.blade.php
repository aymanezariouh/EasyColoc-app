<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Admin Dashboard</h1>
        <p class="text-sm text-gray-500">Global platform overview and moderation tools.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-card class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Users</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalUsers }}</p>
            </x-card>

            <x-card class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Colocations</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalColocations }}</p>
            </x-card>

            <x-card class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Expenses</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalExpenses }}</p>
            </x-card>

            <x-card class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Banned Users</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalBannedUsers }}</p>
            </x-card>
        </div>

        <x-card class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active Colocations</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalActiveColocations }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                    Manage Users
                </a>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                    Back to User Dashboard
                </a>
            </div>
        </x-card>
    </div>
</x-app-layout>
