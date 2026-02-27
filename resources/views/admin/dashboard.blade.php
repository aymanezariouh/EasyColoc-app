<x-app-layout>
    <x-slot name="header">
        <h1 class="ui-title">Admin Dashboard</h1>
        <p class="ui-subtitle">Platform metrics and moderation tools.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <x-stat-card label="Total Users" :value="$totalUsers" />
            <x-stat-card label="Total Colocations" :value="$totalColocations" />
            <x-stat-card label="Total Expenses" :value="$totalExpenses" />
            <x-stat-card label="Banned Users" :value="$totalBannedUsers" />
            <x-stat-card label="Active Colocations" :value="$totalActiveColocations" />
        </div>

        <x-card>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">User Moderation</h2>
                    <p class="mt-1 text-sm text-slate-600">Review users and ban or unban accounts.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn-primary">Open User Management</a>
            </div>
        </x-card>
    </div>
</x-app-layout>
