<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenseService
    ) {
    }

    public function store(Request $request, Colocation $colocation): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $this->expenseService->addExpense($user, $colocation, $validated);

        return redirect()
            ->route('colocations.show', $colocation)
            ->with('status', 'Expense added.');
    }

    public function destroy(Request $request, Expense $expense): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $colocation = $expense->colocation;

        $this->expenseService->deleteExpense($user, $expense);

        return redirect()
            ->route('colocations.show', $colocation)
            ->with('status', 'Expense deleted.');
    }
}
