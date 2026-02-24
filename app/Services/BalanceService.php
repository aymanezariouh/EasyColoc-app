<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Membership;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Support\Collection;

class BalanceService
{
    /**
     * @return array<int, array{user: User, total_paid: string, share: string, balance: string}>
     */
    public function calculateBalances(Colocation $colocation): array
    {
        $rows = $this->buildAdjustedBalanceRows($colocation);

        return collect($rows)->map(function (array $row): array {
            return [
                'user' => $row['user'],
                'total_paid' => $this->formatCents($row['total_paid_cents']),
                'share' => $this->formatCents($row['share_cents']),
                'balance' => $this->formatCents($row['balance_cents']),
            ];
        })->values()->all();
    }

    /**
     * @return array<int, array{from: User, to: User, amount: string}>
     */
    public function simplifiedTransfers(Colocation $colocation): array
    {
        $rows = $this->buildAdjustedBalanceRows($colocation);

        return $this->buildTransfersFromRows($rows);
    }

    /**
     * @return array<int, array{user: User, total_paid_cents: int, share_cents: int, balance_cents: int}>
     */
    private function buildAdjustedBalanceRows(Colocation $colocation): array
    {
        $rows = $this->buildRawBalanceRows($colocation);

        if ($rows === []) {
            return [];
        }

        /** @var Collection<int, Settlement> $settlements */
        $settlements = $colocation->settlements()
            ->orderBy('paid_at')
            ->orderBy('id')
            ->get();

        $rowByUserId = [];
        foreach ($rows as $index => $row) {
            $rowByUserId[(int) $row['user']->id] = $index;
        }

        foreach ($settlements as $settlement) {
            $fromId = (int) $settlement->from_user_id;
            $toId = (int) $settlement->to_user_id;
            $fromActive = isset($rowByUserId[$fromId]);
            $toActive = isset($rowByUserId[$toId]);

            if (! $fromActive && ! $toActive) {
                continue;
            }

            $amountCents = $this->toCents((string) $settlement->amount);

            if ($fromActive && $toActive) {
                $rows[$rowByUserId[$fromId]]['balance_cents'] += $amountCents;
                $rows[$rowByUserId[$toId]]['balance_cents'] -= $amountCents;
                continue;
            }

            if ($fromActive) {
                $rows[$rowByUserId[$fromId]]['balance_cents'] -= $amountCents;
                continue;
            }

            if ($toActive) {
                $rows[$rowByUserId[$toId]]['balance_cents'] -= $amountCents;
            }
        }

        return $rows;
    }

    /**
     * @return array<int, array{user: User, total_paid_cents: int, share_cents: int, balance_cents: int}>
     */
    private function buildRawBalanceRows(Colocation $colocation): array
    {
        /** @var Collection<int, Membership> $memberships */
        $memberships = $colocation->memberships()
            ->with('user')
            ->where('active', true)
            ->whereNull('left_at')
            ->orderBy('user_id')
            ->get();

        if ($memberships->isEmpty()) {
            return [];
        }

        /** @var Collection<int, int> $activeUserIds */
        $activeUserIds = $memberships->pluck('user_id');

        /** @var Collection<int, Expense> $expenses */
        $expenses = $colocation->expenses()
            ->whereIn('payer_id', $activeUserIds)
            ->get();

        $totalExpensesCents = $expenses->sum(
            fn (Expense $expense): int => $this->toCents((string) $expense->amount)
        );

        $memberCount = $memberships->count();
        $baseShareCents = intdiv($totalExpensesCents, $memberCount);
        $shareRemainderCents = $totalExpensesCents % $memberCount;

        $paidByUserCents = [];
        foreach ($activeUserIds as $activeUserId) {
            $paidByUserCents[(int) $activeUserId] = 0;
        }

        foreach ($expenses as $expense) {
            $payerId = (int) $expense->payer_id;
            $paidByUserCents[$payerId] = ($paidByUserCents[$payerId] ?? 0) + $this->toCents((string) $expense->amount);
        }

        $rows = [];
        foreach ($memberships as $index => $membership) {
            $memberUserId = (int) $membership->user_id;
            $shareCents = $baseShareCents + ($index < $shareRemainderCents ? 1 : 0);
            $totalPaidCents = $paidByUserCents[$memberUserId] ?? 0;
            $balanceCents = $totalPaidCents - $shareCents;

            $rows[] = [
                'user' => $membership->user,
                'total_paid_cents' => $totalPaidCents,
                'share_cents' => $shareCents,
                'balance_cents' => $balanceCents,
            ];
        }

        return $rows;
    }

    /**
     * @param  array<int, array{user: User, total_paid_cents: int, share_cents: int, balance_cents: int}>  $rows
     * @return array<int, array{from: User, to: User, amount: string}>
     */
    private function buildTransfersFromRows(array $rows): array
    {
        $debtors = [];
        $creditors = [];

        foreach ($rows as $row) {
            if ($row['balance_cents'] < 0) {
                $debtors[] = [
                    'user' => $row['user'],
                    'remaining_cents' => abs($row['balance_cents']),
                ];
            } elseif ($row['balance_cents'] > 0) {
                $creditors[] = [
                    'user' => $row['user'],
                    'remaining_cents' => $row['balance_cents'],
                ];
            }
        }

        $transfers = [];
        $debtorIndex = 0;
        $creditorIndex = 0;

        while (isset($debtors[$debtorIndex]) && isset($creditors[$creditorIndex])) {
            $amountCents = min(
                $debtors[$debtorIndex]['remaining_cents'],
                $creditors[$creditorIndex]['remaining_cents']
            );

            if ($amountCents <= 0) {
                break;
            }

            $transfers[] = [
                'from' => $debtors[$debtorIndex]['user'],
                'to' => $creditors[$creditorIndex]['user'],
                'amount' => $this->formatCents($amountCents),
            ];

            $debtors[$debtorIndex]['remaining_cents'] -= $amountCents;
            $creditors[$creditorIndex]['remaining_cents'] -= $amountCents;

            if ($debtors[$debtorIndex]['remaining_cents'] === 0) {
                $debtorIndex++;
            }

            if ($creditors[$creditorIndex]['remaining_cents'] === 0) {
                $creditorIndex++;
            }
        }

        return $transfers;
    }

    private function toCents(string $amount): int
    {
        $normalized = str_replace(',', '.', trim($amount));

        if (! preg_match('/^-?\d+(\.\d+)?$/', $normalized)) {
            $normalized = number_format((float) $amount, 2, '.', '');
        }

        $negative = str_starts_with($normalized, '-');
        if ($negative) {
            $normalized = substr($normalized, 1);
        }

        [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '0');
        $fraction = substr(str_pad($fraction, 2, '0'), 0, 2);

        $cents = ((int) $whole * 100) + (int) $fraction;

        return $negative ? -$cents : $cents;
    }

    private function formatCents(int $cents): string
    {
        $negative = $cents < 0;
        $absolute = abs($cents);
        $formatted = number_format($absolute / 100, 2, '.', '');

        if ($formatted === '0.00') {
            return '0.00';
        }

        return $negative ? '-'.$formatted : $formatted;
    }
}
