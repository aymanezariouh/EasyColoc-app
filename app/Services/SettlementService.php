<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\Membership;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SettlementService
{
    public function __construct(
        private readonly BalanceService $balanceService
    ) {
    }

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function markPaid(User $actor, Colocation $colocation, int $fromId, int $toId, string $amount): Settlement
    {
        $this->ensureActorIsActiveMember($actor, $colocation);

        if ($fromId === $toId) {
            throw ValidationException::withMessages([
                'to_user_id' => 'From user and to user must be different.',
            ]);
        }

        $amountCents = $this->toCents($amount);
        if ($amountCents <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Amount must be greater than zero.',
            ]);
        }

        $this->ensureUsersAreActiveMembers($colocation, $fromId, $toId);

        $currentlyOwedCents = $this->currentOwedCents($colocation, $fromId, $toId);
        if ($currentlyOwedCents <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'No debt exists for this transfer.',
            ]);
        }

        if ($amountCents > $currentlyOwedCents) {
            throw ValidationException::withMessages([
                'amount' => 'Amount cannot exceed currently owed amount.',
            ]);
        }

        return DB::transaction(function () use ($colocation, $fromId, $toId, $amountCents): Settlement {
            return Settlement::create([
                'colocation_id' => $colocation->id,
                'from_user_id' => $fromId,
                'to_user_id' => $toId,
                'amount' => $this->formatCents($amountCents),
                'paid_at' => now(),
            ]);
        });
    }

    /**
     * @throws AuthorizationException
     */
    private function ensureActorIsActiveMember(User $actor, Colocation $colocation): void
    {
        $isActiveMember = Membership::query()
            ->where('user_id', $actor->id)
            ->where('colocation_id', $colocation->id)
            ->where('active', true)
            ->whereNull('left_at')
            ->exists();

        if (! $isActiveMember) {
            throw new AuthorizationException('Only active members can mark paid.');
        }
    }

    /**
     * @throws ValidationException
     */
    private function ensureUsersAreActiveMembers(Colocation $colocation, int $fromId, int $toId): void
    {
        $activeIds = Membership::query()
            ->where('colocation_id', $colocation->id)
            ->where('active', true)
            ->whereNull('left_at')
            ->whereIn('user_id', [$fromId, $toId])
            ->pluck('user_id')
            ->unique();

        if ($activeIds->count() !== 2) {
            throw ValidationException::withMessages([
                'from_user_id' => 'Both users must be active members of this colocation.',
            ]);
        }
    }

    private function currentOwedCents(Colocation $colocation, int $fromId, int $toId): int
    {
        $transfers = $this->balanceService->simplifiedTransfers($colocation);

        foreach ($transfers as $transfer) {
            if ((int) $transfer['from']->id === $fromId && (int) $transfer['to']->id === $toId) {
                return $this->toCents((string) $transfer['amount']);
            }
        }

        return 0;
    }

    private function toCents(string $amount): int
    {
        $normalized = str_replace(',', '.', trim($amount));

        if (! preg_match('/^-?\d+(\.\d{1,2})?$/', $normalized)) {
            throw ValidationException::withMessages([
                'amount' => 'Amount must have at most two decimal places.',
            ]);
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
        return number_format($cents / 100, 2, '.', '');
    }
}
