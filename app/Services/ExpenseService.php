<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExpenseService
{
    /**
     * @param  array<string, mixed>  $data
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function addExpense(User $user, Colocation $colocation, array $data): Expense
    {
        $isActiveMember = Membership::query()
            ->where('user_id', $user->id)
            ->where('colocation_id', $colocation->id)
            ->where('active', true)
            ->whereNull('left_at')
            ->exists();

        if (! $isActiveMember) {
            throw new AuthorizationException('Only active members can add expenses.');
        }

        $payerId = $data['payer_id'] ?? $user->id;

        if ($payerId != $user->id) {
            throw new AuthorizationException('Only current user can be payer.');
        }

        $categoryId = $data['category_id'] ?? null;

        if ($categoryId !== null) {
            $category = Category::query()->find($categoryId);

            if (! $category || $category->colocation_id !== $colocation->id) {
                throw ValidationException::withMessages([
                    'category_id' => 'Selected category is invalid for this colocation.',
                ]);
            }
        }

        return DB::transaction(function () use ($colocation, $user, $data, $categoryId): Expense {
            return Expense::create([
                'colocation_id' => $colocation->id,
                'category_id' => $categoryId,
                'payer_id' => $user->id,
                'title' => trim((string) $data['title']),
                'amount' => $data['amount'],
                'expense_date' => $data['expense_date'],
            ]);
        });
    }

    /**
     * @throws AuthorizationException
     */
    public function deleteExpense(User $user, Expense $expense): void
    {
        if ($expense->payer_id !== $user->id) {
            throw new AuthorizationException('Only payer can delete this expense.');
        }

        DB::transaction(function () use ($expense): void {
            $expense->delete();
        });
    }
}
