<x-app-layout>
    <h1>Admin Dashboard</h1>

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
</x-app-layout>
