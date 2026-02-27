<x-app-layout>
    <x-slot name="header">
        @php
            $isOwner = $membership && $membership->role === 'owner';
        @endphp

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Dashboard</a>
                <h1 class="mt-1 text-2xl font-semibold text-slate-900">{{ $colocation->name }}</h1>
                <div class="mt-2">
                    @if ($colocation->status === 'active')
                        <x-badge color="green">Active</x-badge>
                    @else
                        <x-badge color="gray">Cancelled</x-badge>
                    @endif
                </div>
            </div>

            @if ($isOwner)
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('categories.index', $colocation) }}" class="btn-secondary">Categories</a>
                    <a href="{{ route('invitations.create', $colocation) }}" class="btn-primary">Invite Member</a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-card>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Members</h2>
                <x-badge color="blue">{{ $activeMemberships->count() }} active</x-badge>
            </div>

            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Reputation</th>
                            @if ($isOwner)
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activeMemberships as $activeMembership)
                            <tr>
                                <td class="font-medium text-slate-900">{{ $activeMembership->user->name }}</td>
                                <td>{{ $activeMembership->user->email }}</td>
                                <td>
                                    @if ($activeMembership->role === 'owner')
                                        <x-badge color="indigo">Owner</x-badge>
                                    @else
                                        <x-badge color="gray">Member</x-badge>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $rep = (int) $activeMembership->user->reputation;
                                    @endphp
                                    <span class="font-medium {{ $rep > 0 ? 'text-emerald-600' : ($rep < 0 ? 'text-red-600' : 'text-slate-600') }}">{{ $rep }}</span>
                                </td>
                                @if ($isOwner)
                                    <td>
                                        @if ($activeMembership->role !== 'owner')
                                            <form method="POST" action="{{ route('colocations.members.remove', ['colocation' => $colocation, 'user' => $activeMembership->user]) }}">
                                                @csrf
                                                <button type="submit" class="btn-danger btn-sm">Remove</button>
                                            </form>
                                        @else
                                            <span class="text-slate-400">-</span>
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
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Expenses</h2>

                <form method="GET" action="{{ route('colocations.show', $colocation) }}" class="flex items-center gap-2">
                    <select id="month" name="month" class="field-select !mt-0 !w-auto min-w-36">
                        <option value="">All months</option>
                        @foreach ($availableMonths as $month)
                            <option value="{{ $month }}" @selected($selectedMonth === $month)>{{ $month }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-secondary">Filter</button>
                </form>
            </div>

            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Payer</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="font-medium text-slate-900">{{ $expense->title }}</td>
                                <td>{{ number_format((float) $expense->amount, 2) }}</td>
                                <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                <td>{{ $expense->category?->name ?? '-' }}</td>
                                <td>{{ $expense->payer->name }}</td>
                                <td>
                                    @if (auth()->id() === $expense->payer_id)
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">Delete</button>
                                        </form>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-slate-500">No expenses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-slate-900">Add Expense</h2>

            <form method="POST" action="{{ route('expenses.store', $colocation) }}" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                @csrf

                <div>
                    <label for="title" class="field-label">Title</label>
                    <input id="title" name="title" type="text" required class="field-input" placeholder="e.g. Groceries">
                </div>

                <div>
                    <label for="amount" class="field-label">Amount</label>
                    <input id="amount" name="amount" type="number" step="0.01" min="0.01" required class="field-input" placeholder="0.00">
                </div>

                <div>
                    <label for="expense_date" class="field-label">Date</label>
                    <input id="expense_date" name="expense_date" type="date" required class="field-input">
                </div>

                <div>
                    <label for="category_id" class="field-label">Category (optional)</label>
                    <select id="category_id" name="category_id" class="field-select">
                        <option value="">None</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="btn-primary">Add Expense</button>
                </div>
            </form>
        </x-card>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <x-card>
                <h2 class="text-lg font-semibold text-slate-900">Balances</h2>

                <div class="mt-4 ui-table-wrap">
                    <table class="ui-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Total Paid</th>
                                <th>Share</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($balances as $row)
                                @php
                                    $balance = (float) $row['balance'];
                                @endphp
                                <tr>
                                    <td class="font-medium text-slate-900">{{ $row['user']->name }}</td>
                                    <td>{{ $row['total_paid'] }}</td>
                                    <td>{{ $row['share'] }}</td>
                                    <td class="font-medium {{ $balance > 0 ? 'text-emerald-600' : ($balance < 0 ? 'text-red-600' : 'text-slate-700') }}">{{ $row['balance'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-slate-500">No balance data yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card>
                <h2 class="text-lg font-semibold text-slate-900">Who Owes Who</h2>

                @if ($transfers)
                    <ul class="mt-4 space-y-3">
                        @foreach ($transfers as $transfer)
                            <li class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="text-sm text-slate-700">
                                        <span class="font-semibold text-slate-900">{{ $transfer['from']->name }}</span>
                                        owes
                                        <span class="font-semibold text-slate-900">{{ $transfer['to']->name }}</span>
                                        <span class="font-semibold text-red-600">{{ $transfer['amount'] }}</span>
                                    </p>

                                    <form method="POST" action="{{ route('settlements.mark-paid', $colocation) }}">
                                        @csrf
                                        <input type="hidden" name="from_user_id" value="{{ $transfer['from']->id }}">
                                        <input type="hidden" name="to_user_id" value="{{ $transfer['to']->id }}">
                                        <input type="hidden" name="amount" value="{{ $transfer['amount'] }}">
                                        <button type="submit" class="btn-primary btn-sm">Mark Paid</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 text-sm text-slate-600">No pending transfers.</p>
                @endif
            </x-card>
        </div>

        <x-card>
            <h2 class="text-lg font-semibold text-slate-900">Settlements History</h2>

            <div class="mt-4 ui-table-wrap">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Amount</th>
                            <th>Paid At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($settlements as $settlement)
                            <tr>
                                <td>{{ $settlement->fromUser->name }}</td>
                                <td>{{ $settlement->toUser->name }}</td>
                                <td>{{ number_format((float) $settlement->amount, 2) }}</td>
                                <td>{{ $settlement->paid_at?->toDateTimeString() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-slate-500">No settlements yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-slate-900">Colocation Actions</h2>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($membership && $membership->role !== 'owner')
                    <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                        @csrf
                        <button type="submit" class="btn-danger">Leave Colocation</button>
                    </form>
                @endif

                @if ($isOwner)
                    <form method="POST" action="{{ route('colocations.cancel', $colocation) }}">
                        @csrf
                        <button type="submit" class="btn-danger">Cancel Colocation</button>
                    </form>
                @endif
            </div>
        </x-card>
    </div>
</x-app-layout>
