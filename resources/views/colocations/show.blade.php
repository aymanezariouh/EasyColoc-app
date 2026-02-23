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
