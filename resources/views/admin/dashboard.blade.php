<x-app-layout>
    <x-slot name="header">
        <h2>Admin Dashboard</h2>
    </x-slot>

    <ul>
        <li>Total users: {{ $totalUsers }}</li>
        <li>Total colocations: {{ $totalColocations }}</li>
        <li>Total expenses: {{ $totalExpenses }}</li>
        <li>Total banned users: {{ $totalBannedUsers }}</li>
        <li>Total active colocations: {{ $totalActiveColocations }}</li>
    </ul>

    <p>
        <a href="{{ route('admin.users.index') }}">Manage users</a>
    </p>

    <p>
        <a href="{{ route('dashboard') }}">Back to user dashboard</a>
    </p>
</x-app-layout>
