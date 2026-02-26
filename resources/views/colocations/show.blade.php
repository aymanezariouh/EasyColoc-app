<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Dashboard</a>
                <h1 class="mt-1 text-2xl font-semibold text-gray-900">{{ $colocation->name }}</h1>
                <div class="mt-2">
                    @if ($colocation->status === 'active')
                        <x-badge color="green">Active</x-badge>
                    @else
                        <x-badge color="gray">Cancelled</x-badge>
                    @endif
                </div>
            </div>

            @php
                $isOwner = $membership && $membership->role === 'owner';
            @endphp

            @if ($isOwner)
                <div class="flex items-center gap-2">
                    <a href="{{ route('categories.index', $colocation) }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300">
                        Categories
                    </a>
                    <a href="{{ route('invitations.create', $colocation) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Invite Member
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-card>
            <h2 class="text-lg font-semibold text-gray-900">Members</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="px-3 py-2">Name</th>
                            <th class="px-3 py-2">Email</th>
                            <th class="px-3 py-2">Role</th>
                            <th class="px-3 py-2">Reputation</th>
                            @if ($isOwner)
                                <th class="px-3 py-2">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($activeMemberships as $activeMembership)
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $activeMembership->user->name }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $activeMembership->user->email }}</td>
                                <td class="px-3 py-2">
                                    @if ($activeMembership->role === 'owner')
                                        <x-badge color="indigo">Owner</x-badge>
                                    @else
                                        <x-badge color="gray">Member</x-badge>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @php
                                        $rep = (int) $activeMembership->user->reputation;
                                    @endphp
                                    @if ($rep > 0)
                                        <span class="font-medium text-green-600">{{ $rep }}</span>
                                    @elseif ($rep < 0)
                                        <span class="font-medium text-red-600">{{ $rep }}</span>
                                    @else
                                        <span class="font-medium text-gray-600">{{ $rep }}</span>
                                    @endif
                                </td>
                                @if ($isOwner)
                                    <td class="px-3 py-2">
                                        @if ($activeMembership->role !== 'owner')
                                            <form method="POST" action="{{ route('colocations.members.remove', ['colocation' => $colocation, 'user' => $activeMembership->user]) }}">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">
                                                    Remove
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Expenses</h2>

                <form method="GET" action="{{ route('colocations.show', $colocation) }}" class="flex items-center gap-2">
                    <select id="month" name="month" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All months</option>
                        @foreach ($availableMonths as $month)
                            <option value="{{ $month }}" @selected($selectedMonth === $month)>{{ $month }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300">Filter</button>
                </form>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="px-3 py-2">Title</th>
                            <th class="px-3 py-2">Amount</th>
                            <th class="px-3 py-2">Date</th>
                            <th class="px-3 py-2">Category</th>
                            <th class="px-3 py-2">Payer</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $expense->title }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ number_format((float) $expense->amount, 2) }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $expense->expense_date->format('Y-m-d') }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $expense->category?->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $expense->payer->name }}</td>
                                <td class="px-3 py-2">
                                    @if (auth()->id() === $expense->payer_id)
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-gray-500">No expenses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-gray-900">Add Expense</h2>
            <form method="POST" action="{{ route('expenses.store', $colocation) }}" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input id="title" name="title" type="text" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                    <input id="amount" name="amount" type="number" step="0.01" min="0.01" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="expense_date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input id="expense_date" name="expense_date" type="date" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category (optional)</label>
                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">None</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Add Expense
                    </button>
                </div>
            </form>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-gray-900">Balances</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="px-3 py-2">Member</th>
                            <th class="px-3 py-2">Total Paid</th>
                            <th class="px-3 py-2">Share</th>
                            <th class="px-3 py-2">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($balances as $row)
                            @php
                                $balance = (float) $row['balance'];
                            @endphp
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $row['user']->name }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $row['total_paid'] }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $row['share'] }}</td>
                                <td class="px-3 py-2 font-medium {{ $balance > 0 ? 'text-green-600' : ($balance < 0 ? 'text-red-600' : 'text-gray-700') }}">{{ $row['balance'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-gray-500">No balance data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-gray-900">Who Owes Who</h2>

            @if ($transfers)
                <ul class="mt-4 space-y-3">
                    @foreach ($transfers as $transfer)
                        <li class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-gray-800">
                                <span class="font-medium">{{ $transfer['from']->name }}</span>
                                owes
                                <span class="font-medium">{{ $transfer['to']->name }}</span>
                                :
                                <span class="font-semibold text-red-600">{{ $transfer['amount'] }}</span>
                            </p>

                            <form method="POST" action="{{ route('settlements.mark-paid', $colocation) }}">
                                @csrf
                                <input type="hidden" name="from_user_id" value="{{ $transfer['from']->id }}">
                                <input type="hidden" name="to_user_id" value="{{ $transfer['to']->id }}">
                                <input type="hidden" name="amount" value="{{ $transfer['amount'] }}">
                                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                    Mark Paid
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-4 text-sm text-gray-600">No pending transfers.</p>
            @endif
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-gray-900">Settlements History</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="px-3 py-2">From</th>
                            <th class="px-3 py-2">To</th>
                            <th class="px-3 py-2">Amount</th>
                            <th class="px-3 py-2">Paid At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($settlements as $settlement)
                            <tr>
                                <td class="px-3 py-2 text-gray-700">{{ $settlement->fromUser->name }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $settlement->toUser->name }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ number_format((float) $settlement->amount, 2) }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $settlement->paid_at?->toDateTimeString() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-gray-500">No settlements yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-gray-900">Colocation Actions</h2>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($membership && $membership->role !== 'owner')
                    <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                            Leave Colocation
                        </button>
                    </form>
                @endif

                @if ($isOwner)
                    <form method="POST" action="{{ route('colocations.cancel', $colocation) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                            Cancel Colocation
                        </button>
                    </form>
                @endif
            </div>
        </x-card>
    </div>
</x-app-layout>
