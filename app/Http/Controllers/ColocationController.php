<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\User;
use App\Services\BalanceService;
use App\Services\ColocationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColocationController extends Controller
{
    public function __construct(
        private readonly ColocationService $colocationService,
        private readonly BalanceService $balanceService
    ) {
    }

    public function show(Request $request, Colocation $colocation): View
    {
        /** @var User $user */
        $user = $request->user();

        $membership = $colocation->memberships()
            ->where('user_id', $user->id)
            ->where('active', true)
            ->whereNull('left_at')
            ->first();

        if (! $membership) {
            abort(403, 'Only active members can view this colocation.');
        }

        $selectedMonth = $request->query('month');

        $expensesQuery = Expense::query()
            ->with(['category', 'payer'])
            ->where('colocation_id', $colocation->id)
            ->orderByDesc('expense_date')
            ->orderByDesc('id');

        if (is_string($selectedMonth) && preg_match('/^\d{4}-\d{2}$/', $selectedMonth) === 1) {
            $start = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
            $end = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

            $expensesQuery->whereBetween('expense_date', [
                $start->toDateString(),
                $end->toDateString(),
            ]);
        } else {
            $selectedMonth = null;
        }

        $expenses = $expensesQuery->get();

        $availableMonths = Expense::query()
            ->where('colocation_id', $colocation->id)
            ->orderByDesc('expense_date')
            ->get(['expense_date'])
            ->map(fn (Expense $expense): string => Carbon::parse($expense->expense_date)->format('Y-m'))
            ->unique()
            ->values();

        $categories = $colocation->categories()
            ->orderBy('name')
            ->get();

        $activeMemberships = $colocation->memberships()
            ->with('user')
            ->where('active', true)
            ->whereNull('left_at')
            ->orderByRaw("CASE WHEN role = 'owner' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->get();

        $balances = $this->balanceService->calculateBalances($colocation);
        $transfers = $this->balanceService->simplifiedTransfers($colocation);
        $settlements = $colocation->settlements()
            ->with(['fromUser', 'toUser'])
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->get();

        return view('colocations.show', [
            'colocation' => $colocation,
            'membership' => $membership,
            'expenses' => $expenses,
            'availableMonths' => $availableMonths,
            'selectedMonth' => $selectedMonth,
            'categories' => $categories,
            'activeMemberships' => $activeMemberships,
            'balances' => $balances,
            'transfers' => $transfers,
            'settlements' => $settlements,
        ]);
    }

    public function leave(Request $request, Colocation $colocation): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->colocationService->leaveColocation($user, $colocation);

        return redirect()
            ->route('colocations.show', $colocation)
            ->with('status', 'You left the colocation.');
    }

    public function cancel(Request $request, Colocation $colocation): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->colocationService->cancelColocation($user, $colocation);

        return redirect()
            ->route('colocations.show', $colocation)
            ->with('status', 'Colocation cancelled.');
    }

    public function removeMember(Request $request, Colocation $colocation, User $user): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $this->colocationService->removeMember($actor, $colocation, $user);

        return redirect()
            ->route('colocations.show', $colocation)
            ->with('status', 'Member removed.');
    }
}
