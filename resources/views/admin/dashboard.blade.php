<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Admin Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Platform statistics overview.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <x-stat-card label="Total Users" :value="$totalUsers" />
            <x-stat-card label="Total Colocations" :value="$totalColocations" />
            <x-stat-card label="Total Expenses" :value="$totalExpenses" />
            <x-stat-card label="Banned Users" :value="$totalBannedUsers" />
            <x-stat-card label="Active Colocations" :value="$totalActiveColocations" />
        </div>

        <x-card>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Go to User Management
            </a>
        </x-card>
    </div>
</x-app-layout>
