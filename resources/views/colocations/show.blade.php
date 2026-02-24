<x-app-layout>
    <h1>{{ $colocation->name }}</h1>
    <p>Status: {{ $colocation->status }}</p>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <p>{{ $errors->first() }}</p>
    @endif

    <h2>Filter Expenses by Month</h2>
    <form method="GET" action="{{ route('colocations.show', $colocation) }}">
        <label for="month">Month</label>
        <select id="month" name="month">
            <option value="">All</option>
            @foreach ($availableMonths as $month)
                <option value="{{ $month }}" @selected($selectedMonth === $month)>
                    {{ $month }}
                </option>
            @endforeach
        </select>
        <button type="submit">Apply</button>
    </form>

    <h2>Expenses</h2>
    <table border="1" cellpadding="4" cellspacing="0">
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
                    <td>{{ $expense->title }}</td>
                    <td>{{ $expense->amount }}</td>
                    <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                    <td>{{ $expense->category?->name ?? '-' }}</td>
                    <td>{{ $expense->payer->name }}</td>
                    <td>
                        @if (auth()->id() === $expense->payer_id)
                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No expenses found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Add Expense</h2>
    <form method="POST" action="{{ route('expenses.store', $colocation) }}">
        @csrf
        <div>
            <label for="title">Title</label>
            <input id="title" name="title" type="text" required>
        </div>
        <div>
            <label for="amount">Amount</label>
            <input id="amount" name="amount" type="number" step="0.01" min="0.01" required>
        </div>
        <div>
            <label for="expense_date">Date</label>
            <input id="expense_date" name="expense_date" type="date" required>
        </div>
        <div>
            <label for="category_id">Category (optional)</label>
            <select id="category_id" name="category_id">
                <option value="">None</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Add Expense</button>
    </form>

    <h2>Balances</h2>
    <table border="1" cellpadding="4" cellspacing="0">
        <thead>
            <tr>
                <th>Member</th>
                <th>Total Paid</th>
                <th>Share</th>
                <th>Balance</th>
                @if ($membership && $membership->role === 'owner')
                    <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($balances as $row)
                <tr>
                    <td>{{ $row['user']->name }}</td>
                    <td>{{ $row['total_paid'] }}</td>
                    <td>{{ $row['share'] }}</td>
                    <td>{{ $row['balance'] }}</td>
                    @if ($membership && $membership->role === 'owner')
                        <td>
                            @if ($row['user']->id !== $colocation->owner_id)
                                <form method="POST" action="{{ route('colocations.members.remove', ['colocation' => $colocation, 'user' => $row['user']]) }}">
                                    @csrf
                                    <button type="submit">Remove</button>
                                </form>
                            @else
                                -
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ ($membership && $membership->role === 'owner') ? 5 : 4 }}">No active members.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Who Owes Who</h2>
    <ul>
        @forelse ($transfers as $transfer)
            <li>
                {{ $transfer['from']->name }} owes {{ $transfer['to']->name }} : {{ $transfer['amount'] }}
                <form method="POST" action="{{ route('settlements.mark-paid', $colocation) }}">
                    @csrf
                    <input type="hidden" name="from_user_id" value="{{ $transfer['from']->id }}">
                    <input type="hidden" name="to_user_id" value="{{ $transfer['to']->id }}">
                    <input type="hidden" name="amount" value="{{ $transfer['amount'] }}">
                    <button type="submit">Mark paid</button>
                </form>
            </li>
        @empty
            <li>No transfers needed.</li>
        @endforelse
    </ul>

    <h2>Past Settlements</h2>
    <table border="1" cellpadding="4" cellspacing="0">
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
                    <td>{{ $settlement->amount }}</td>
                    <td>{{ $settlement->paid_at?->toDateTimeString() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No settlements yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($membership && $membership->role !== 'owner')
        <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
            @csrf
            <button type="submit">Leave Colocation</button>
        </form>
    @endif

    @if ($membership && $membership->role === 'owner')
        <p>
            <a href="{{ route('invitations.create', $colocation) }}">Invite by email</a>
        </p>
        <p>
            <a href="{{ route('categories.index', $colocation) }}">Manage categories</a>
        </p>

        <form method="POST" action="{{ route('colocations.cancel', $colocation) }}">
            @csrf
            <button type="submit">Cancel Colocation</button>
        </form>
    @endif
</x-app-layout>
