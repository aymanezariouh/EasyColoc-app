<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $colocation->name }}</h1>
        <p class="text-sm text-gray-500">Manage members, expenses, balances, and settlements.</p>
    </x-slot>

    @php
        $isOwner = $membership && $membership->role === 'owner';
    @endphp

    <div class="space-y-6">
        <x-card class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Colocation Status</p>
                    @if ($colocation->status === 'active')
                        <x-badge color="green">Active</x-badge>
                    @else
                        <x-badge color="gray">Cancelled</x-badge>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                        Back to Dashboard
                    </a>
                    @if ($isOwner)
                        <a href="{{ route('categories.index', $colocation) }}"
                           class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                            Categories
                        </a>
                        <a href="{{ route('invitations.create', $colocation) }}"
                           class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                            Invite Member
                        </a>
                    @endif
                </div>
            </div>
        </x-card>

        <x-card class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Members</h2>
                <x-badge color="gray">{{ $activeMemberships->count() }} active</x-badge>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <th class="px-3 py-3">Name</th>
                            <th class="px-3 py-3">Role</th>
                            <th class="px-3 py-3">Reputation</th>
                            @if ($isOwner)
                                <th class="px-3 py-3">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($activeMemberships as $activeMembership)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3">
                                    <p class="font-medium text-gray-900">{{ $activeMembership->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $activeMembership->user->email }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    @if ($activeMembership->role === 'owner')
                                        <x-badge color="indigo">Owner</x-badge>
                                    @else
                                        <x-badge color="gray">Member</x-badge>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    @php
                                        $reputation = (int) $activeMembership->user->reputation;
                                        $reputationColor = $reputation > 0 ? 'green' : ($reputation < 0 ? 'red' : 'gray');
                                    @endphp
                                    <x-badge :color="$reputationColor">{{ $reputation }}</x-badge>
                                </td>
                                @if ($isOwner)
                                    <td class="px-3 py-3">
                                        @if ($activeMembership->role !== 'owner')
                                            <form method="POST" action="{{ route('colocations.members.remove', ['colocation' => $colocation, 'user' => $activeMembership->user]) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-1.5 text-xs font-medium transition">
                                                    Remove
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isOwner ? 4 : 3 }}" class="px-3 py-4 text-sm text-gray-500">No active members.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Expenses</h2>
                <form method="GET" action="{{ route('colocations.show', $colocation) }}" class="flex items-center gap-2">
                    <label for="month" class="text-sm font-medium text-gray-600">Month</label>
                    <select id="month" name="month" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All</option>
                        @foreach ($availableMonths as $month)
                            <option value="{{ $month }}" @selected($selectedMonth === $month)>{{ $month }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition">
                        Apply
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <th class="px-3 py-3">Title</th>
                            <th class="px-3 py-3">Amount</th>
                            <th class="px-3 py-3">Date</th>
                            <th class="px-3 py-3">Category</th>
                            <th class="px-3 py-3">Payer</th>
                            <th class="px-3 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($expenses as $expense)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 font-medium text-gray-900">{{ $expense->title }}</td>
                                <td class="px-3 py-3">{{ number_format((float) $expense->amount, 2) }}</td>
                                <td class="px-3 py-3">{{ $expense->expense_date->format('Y-m-d') }}</td>
                                <td class="px-3 py-3">{{ $expense->category?->name ?? '-' }}</td>
                                <td class="px-3 py-3">{{ $expense->payer->name }}</td>
                                <td class="px-3 py-3">
                                    @if (auth()->id() === $expense->payer_id)
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-1.5 text-xs font-medium transition">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-sm text-gray-500">No expenses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Add Expense</h2>
            <form method="POST" action="{{ route('expenses.store', $colocation) }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @csrf
                <div class="space-y-1">
                    <label for="title" class="text-sm font-medium text-gray-700">Title</label>
                    <input id="title" name="title" type="text" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="space-y-1">
                    <label for="amount" class="text-sm font-medium text-gray-700">Amount</label>
                    <input id="amount" name="amount" type="number" step="0.01" min="0.01" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="space-y-1">
                    <label for="expense_date" class="text-sm font-medium text-gray-700">Date</label>
                    <input id="expense_date" name="expense_date" type="date" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="space-y-1">
                    <label for="category_id" class="text-sm font-medium text-gray-700">Category (optional)</label>
                    <select id="category_id" name="category_id"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">None</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit"
                            class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                        Add Expense
                    </button>
                </div>
            </form>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Balances</h2>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <th class="px-3 py-3">Member</th>
                            <th class="px-3 py-3">Total Paid</th>
                            <th class="px-3 py-3">Share</th>
                            <th class="px-3 py-3">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($balances as $row)
                            @php
                                $balance = (float) $row['balance'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 font-medium text-gray-900">{{ $row['user']->name }}</td>
                                <td class="px-3 py-3">{{ $row['total_paid'] }}</td>
                                <td class="px-3 py-3">{{ $row['share'] }}</td>
                                <td class="px-3 py-3 font-semibold {{ $balance > 0 ? 'text-green-600' : ($balance < 0 ? 'text-red-600' : 'text-gray-700') }}">
                                    {{ $row['balance'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-sm text-gray-500">No balance data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Who Owes Who</h2>
            <div class="grid gap-3 md:grid-cols-2">
                @forelse ($transfers as $transfer)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-3">
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold text-gray-900">{{ $transfer['from']->name }}</span>
                            owes
                            <span class="font-semibold text-gray-900">{{ $transfer['to']->name }}</span>
                            :
                            <span class="font-semibold text-red-600">{{ $transfer['amount'] }}</span>
                        </p>
                        <form method="POST" action="{{ route('settlements.mark-paid', $colocation) }}">
                            @csrf
                            <input type="hidden" name="from_user_id" value="{{ $transfer['from']->id }}">
                            <input type="hidden" name="to_user_id" value="{{ $transfer['to']->id }}">
                            <input type="hidden" name="amount" value="{{ $transfer['amount'] }}">
                            <button type="submit"
                                    class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                                Mark Paid
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No transfers needed.</p>
                @endforelse
            </div>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Settlements History</h2>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <th class="px-3 py-3">From</th>
                            <th class="px-3 py-3">To</th>
                            <th class="px-3 py-3">Amount</th>
                            <th class="px-3 py-3">Paid At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($settlements as $settlement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3">{{ $settlement->fromUser->name }}</td>
                                <td class="px-3 py-3">{{ $settlement->toUser->name }}</td>
                                <td class="px-3 py-3">{{ number_format((float) $settlement->amount, 2) }}</td>
                                <td class="px-3 py-3">{{ $settlement->paid_at?->toDateTimeString() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-sm text-gray-500">No settlements yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
            <div class="flex flex-wrap items-center gap-2">
                @if ($membership && $membership->role !== 'owner')
                    <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                            Leave Colocation
                        </button>
                    </form>
                @endif

                @if ($isOwner)
                    <form method="POST" action="{{ route('colocations.cancel', $colocation) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                            Cancel Colocation
                        </button>
                    </form>
                @endif
            </div>
        </x-card>
    </div>
</x-app-layout>
