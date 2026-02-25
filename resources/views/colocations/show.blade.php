<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-indigo-600 transition mb-2">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    Dashboard
                </a>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $colocation->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage members, expenses, balances, and settlements.</p>
            </div>
        </div>
    </x-slot>

    @php
        $isOwner = $membership && $membership->role === 'owner';
    @endphp

    <div class="space-y-8">

        {{-- Status Bar --}}
        <x-card class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-500">Status</span>
                        @if ($colocation->status === 'active')
                            <x-badge color="green">Active</x-badge>
                        @else
                            <x-badge color="gray">Cancelled</x-badge>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if ($isOwner)
                        <a href="{{ route('categories.index', $colocation) }}"
                           class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 rounded-xl px-4 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                            Categories
                        </a>
                        <a href="{{ route('invitations.create', $colocation) }}"
                           class="inline-flex items-center gap-1.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm shadow-indigo-200 hover:shadow-md transition-all duration-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>
                            Invite Member
                        </a>
                    @endif
                </div>
            </div>
        </x-card>

        {{-- Members --}}
        <x-card class="space-y-5">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 flex-shrink-0">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Members</h2>
                </div>
                <x-badge color="gray">{{ $activeMemberships->count() }} active</x-badge>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            <th class="px-4 py-3.5">Member</th>
                            <th class="px-4 py-3.5">Role</th>
                            <th class="px-4 py-3.5">Reputation</th>
                            @if ($isOwner)
                                <th class="px-4 py-3.5">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($activeMemberships as $activeMembership)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-500 text-xs font-bold text-white flex-shrink-0">
                                            {{ strtoupper(substr($activeMembership->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $activeMembership->user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $activeMembership->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($activeMembership->role === 'owner')
                                        <x-badge color="indigo">Owner</x-badge>
                                    @else
                                        <x-badge color="gray">Member</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    @php
                                        $reputation = (int) $activeMembership->user->reputation;
                                        $reputationColor = $reputation > 0 ? 'green' : ($reputation < 0 ? 'red' : 'gray');
                                    @endphp
                                    <x-badge :color="$reputationColor">{{ $reputation }}</x-badge>
                                </td>
                                @if ($isOwner)
                                    <td class="px-4 py-3.5">
                                        @if ($activeMembership->role !== 'owner')
                                            <form method="POST" action="{{ route('colocations.members.remove', ['colocation' => $colocation, 'user' => $activeMembership->user]) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all duration-200">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>
                                                    Remove
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isOwner ? 4 : 3 }}" class="px-4 py-8 text-center text-sm text-gray-400">No active members.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Expenses --}}
        <x-card class="space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 flex-shrink-0">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Expenses</h2>
                </div>
                <form method="GET" action="{{ route('colocations.show', $colocation) }}" class="flex items-center gap-2">
                    <select id="month" name="month" class="rounded-xl border-gray-200 bg-gray-50 text-sm font-medium text-gray-600 focus:border-indigo-500 focus:ring-indigo-500 transition">
                        <option value="">All months</option>
                        @foreach ($availableMonths as $month)
                            <option value="{{ $month }}" @selected($selectedMonth === $month)>{{ $month }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="inline-flex items-center bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 rounded-xl px-4 py-2 text-sm font-medium transition-all duration-200">
                        Filter
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            <th class="px-4 py-3.5">Title</th>
                            <th class="px-4 py-3.5">Amount</th>
                            <th class="px-4 py-3.5">Date</th>
                            <th class="px-4 py-3.5">Category</th>
                            <th class="px-4 py-3.5">Payer</th>
                            <th class="px-4 py-3.5">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($expenses as $expense)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3.5 font-semibold text-gray-900">{{ $expense->title }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="font-semibold text-gray-900">{{ number_format((float) $expense->amount, 2) }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-gray-500">{{ $expense->expense_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3.5">
                                    @if ($expense->category?->name)
                                        <x-badge color="blue">{{ $expense->category->name }}</x-badge>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-md bg-gray-100 text-[10px] font-bold text-gray-500">
                                            {{ strtoupper(substr($expense->payer->name, 0, 1)) }}
                                        </div>
                                        <span class="text-gray-700">{{ $expense->payer->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if (auth()->id() === $expense->payer_id)
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all duration-200">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">No expenses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Add Expense --}}
        <x-card class="space-y-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Add Expense</h2>
            </div>

            <form method="POST" action="{{ route('expenses.store', $colocation) }}" class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                @csrf
                <div class="space-y-1.5">
                    <label for="title" class="block text-sm font-semibold text-gray-700">Title</label>
                    <input id="title" name="title" type="text" required placeholder="e.g. Groceries"
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition">
                </div>
                <div class="space-y-1.5">
                    <label for="amount" class="block text-sm font-semibold text-gray-700">Amount</label>
                    <input id="amount" name="amount" type="number" step="0.01" min="0.01" required placeholder="0.00"
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition">
                </div>
                <div class="space-y-1.5">
                    <label for="expense_date" class="block text-sm font-semibold text-gray-700">Date</label>
                    <input id="expense_date" name="expense_date" type="date" required
                           class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition">
                </div>
                <div class="space-y-1.5">
                    <label for="category_id" class="block text-sm font-semibold text-gray-700">Category <span class="font-normal text-gray-400">(optional)</span></label>
                    <select id="category_id" name="category_id"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm text-gray-600 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition">
                        <option value="">None</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-sm shadow-indigo-200 hover:shadow-md transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Add Expense
                    </button>
                </div>
            </form>
        </x-card>

        {{-- Balances --}}
        <x-card class="space-y-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z" /></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Balances</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            <th class="px-4 py-3.5">Member</th>
                            <th class="px-4 py-3.5">Total Paid</th>
                            <th class="px-4 py-3.5">Share</th>
                            <th class="px-4 py-3.5">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($balances as $row)
                            @php
                                $balance = (float) $row['balance'];
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-md bg-gray-100 text-[10px] font-bold text-gray-500">
                                            {{ strtoupper(substr($row['user']->name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $row['user']->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-gray-700">{{ $row['total_paid'] }}</td>
                                <td class="px-4 py-3.5 text-gray-700">{{ $row['share'] }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1 font-bold text-sm {{ $balance > 0 ? 'text-emerald-600' : ($balance < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                        @if ($balance > 0)
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" /></svg>
                                        @elseif ($balance < 0)
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5l15 15m0 0V8.25m0 11.25H8.25" /></svg>
                                        @endif
                                        {{ $row['balance'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">No balance data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Who Owes Who --}}
        <x-card class="space-y-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 flex-shrink-0">
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Who Owes Who</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @forelse ($transfers as $transfer)
                    <div class="rounded-xl border border-gray-100 bg-gradient-to-br from-gray-50/80 to-white p-5 space-y-4 hover:border-gray-200 hover:shadow-sm transition-all duration-200">
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-xs font-bold text-red-600 flex-shrink-0">
                                {{ strtoupper(substr($transfer['from']->name, 0, 1)) }}
                            </div>
                            <svg class="h-4 w-4 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-xs font-bold text-emerald-600 flex-shrink-0">
                                {{ strtoupper(substr($transfer['to']->name, 0, 1)) }}
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold text-gray-900">{{ $transfer['from']->name }}</span>
                            <span class="text-gray-400">owes</span>
                            <span class="font-semibold text-gray-900">{{ $transfer['to']->name }}</span>
                        </p>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xl font-bold text-red-600">{{ $transfer['amount'] }}</span>
                            <form method="POST" action="{{ route('settlements.mark-paid', $colocation) }}">
                                @csrf
                                <input type="hidden" name="from_user_id" value="{{ $transfer['from']->id }}">
                                <input type="hidden" name="to_user_id" value="{{ $transfer['to']->id }}">
                                <input type="hidden" name="amount" value="{{ $transfer['amount'] }}">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl px-4 py-2 text-sm font-semibold shadow-sm shadow-emerald-200 hover:shadow-md transition-all duration-200">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    Mark Paid
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="sm:col-span-2 text-center py-8">
                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <p class="mt-2 text-sm text-gray-400">All settled up! No transfers needed.</p>
                    </div>
                @endforelse
            </div>
        </x-card>

        {{-- Settlements History --}}
        <x-card class="space-y-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 flex-shrink-0">
                    <svg class="h-5 w-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Settlements History</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            <th class="px-4 py-3.5">From</th>
                            <th class="px-4 py-3.5">To</th>
                            <th class="px-4 py-3.5">Amount</th>
                            <th class="px-4 py-3.5">Paid At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($settlements as $settlement)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-md bg-gray-100 text-[10px] font-bold text-gray-500">
                                            {{ strtoupper(substr($settlement->fromUser->name, 0, 1)) }}
                                        </div>
                                        <span class="text-gray-700">{{ $settlement->fromUser->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-md bg-gray-100 text-[10px] font-bold text-gray-500">
                                            {{ strtoupper(substr($settlement->toUser->name, 0, 1)) }}
                                        </div>
                                        <span class="text-gray-700">{{ $settlement->toUser->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-gray-900">{{ number_format((float) $settlement->amount, 2) }}</td>
                                <td class="px-4 py-3.5 text-gray-500">{{ $settlement->paid_at?->toDateTimeString() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">No settlements yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Actions --}}
        <x-card class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900">Danger Zone</h2>
            <div class="flex flex-wrap items-center gap-3">
                @if ($membership && $membership->role !== 'owner')
                    <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                            Leave Colocation
                        </button>
                    </form>
                @endif

                @if ($isOwner)
                    <form method="POST" action="{{ route('colocations.cancel', $colocation) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm shadow-red-200 hover:shadow-md transition-all duration-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                            Cancel Colocation
                        </button>
                    </form>
                @endif
            </div>
        </x-card>
    </div>
</x-app-layout>
