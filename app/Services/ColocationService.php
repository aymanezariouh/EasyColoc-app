<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\Membership;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ColocationService
{
    public function __construct(
        private readonly BalanceService $balanceService
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function createColocation(User $user, string $name): Colocation
    {
        $normalizedName = trim($name);

        if ($normalizedName === '') {
            throw ValidationException::withMessages([
                'name' => 'Colocation name is required.',
            ]);
        }

        return DB::transaction(function () use ($user, $normalizedName): Colocation {
            $colocation = Colocation::create([
                'name' => $normalizedName,
                'owner_id' => $user->id,
                'status' => 'active',
                'cancelled_at' => null,
            ]);

            Membership::create([
                'user_id' => $user->id,
                'colocation_id' => $colocation->id,
                'role' => 'owner',
                'active' => true,
                'left_at' => null,
            ]);

            return $colocation;
        });
    }

    /**
     * @throws AuthorizationException
     */
    public function leaveColocation(User $user, Colocation $colocation): void
    {
        DB::transaction(function () use ($user, $colocation): void {
            $this->ensureColocationIsActive($colocation);

            $membership = $colocation->memberships()
                ->where('user_id', $user->id)
                ->where('active', true)
                ->whereNull('left_at')
                ->first();

            if (! $membership) {
                throw new AuthorizationException('Only active members can leave this colocation.');
            }

            if ($membership->role === 'owner') {
                throw new AuthorizationException('Owner cannot leave directly.');
            }

            $balancesByUser = $this->balanceCentsByUserId($colocation);
            $balanceCents = $balancesByUser[$user->id] ?? 0;

            $this->applyReputation($user, $balanceCents);

            $membership->forceFill([
                'active' => false,
                'left_at' => now(),
            ])->save();
        });
    }

    /**
     * @throws AuthorizationException
     */
    public function cancelColocation(User $user, Colocation $colocation): void
    {
        DB::transaction(function () use ($user, $colocation): void {
            $this->ensureColocationIsActive($colocation);

            $ownerMembership = $colocation->memberships()
                ->where('user_id', $user->id)
                ->where('role', 'owner')
                ->where('active', true)
                ->whereNull('left_at')
                ->first();

            if (! $ownerMembership) {
                throw new AuthorizationException('Only the owner can cancel this colocation.');
            }

            $activeMemberships = $colocation->memberships()
                ->with('user')
                ->where('active', true)
                ->whereNull('left_at')
                ->get();

            $balancesByUser = $this->balanceCentsByUserId($colocation);

            foreach ($activeMemberships as $membership) {
                $memberUser = $membership->user;
                $memberBalanceCents = $balancesByUser[$memberUser->id] ?? 0;
                $this->applyReputation($memberUser, $memberBalanceCents);
            }

            $colocation->forceFill([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ])->save();

            $colocation->memberships()
                ->where('active', true)
                ->update([
                    'active' => false,
                    'left_at' => now(),
                    'updated_at' => now(),
                ]);
        });
    }

    /**
     * @throws AuthorizationException
     */
    public function removeMember(User $owner, Colocation $colocation, User $member): void
    {
        DB::transaction(function () use ($owner, $colocation, $member): void {
            $this->ensureColocationIsActive($colocation);

            $ownerMembership = $colocation->memberships()
                ->where('user_id', $owner->id)
                ->where('role', 'owner')
                ->where('active', true)
                ->whereNull('left_at')
                ->first();

            if (! $ownerMembership) {
                throw new AuthorizationException('Only owner can remove members.');
            }

            $targetMembership = $colocation->memberships()
                ->where('user_id', $member->id)
                ->where('active', true)
                ->whereNull('left_at')
                ->first();

            if (! $targetMembership) {
                throw new AuthorizationException('Target user is not an active member.');
            }

            if ($targetMembership->role === 'owner') {
                throw new AuthorizationException('Owner cannot be removed.');
            }

            $balancesByUser = $this->balanceCentsByUserId($colocation);
            $targetBalanceCents = $balancesByUser[$member->id] ?? 0;

            if ($targetBalanceCents < 0) {
                Settlement::create([
                    'colocation_id' => $colocation->id,
                    'from_user_id' => $owner->id,
                    'to_user_id' => $member->id,
                    'amount' => $this->formatCents(abs($targetBalanceCents)),
                    'paid_at' => now(),
                ]);
            }

            $this->applyReputation($member, $targetBalanceCents);

            $targetMembership->forceFill([
                'active' => false,
                'left_at' => now(),
            ])->save();
        });
    }

    /**
     * @return array<int, int>
     */
    private function balanceCentsByUserId(Colocation $colocation): array
    {
        $rows = $this->balanceService->calculateBalances($colocation);
        $result = [];

        foreach ($rows as $row) {
            $userId = (int) $row['user']->id;
            $result[$userId] = $this->toCents((string) $row['balance']);
        }

        return $result;
    }

    private function applyReputation(User $user, int $balanceCents): void
    {
        if ($balanceCents < 0) {
            $user->decrement('reputation');
            return;
        }

        $user->increment('reputation');
    }

    /**
     * @throws AuthorizationException
     */
    private function ensureColocationIsActive(Colocation $colocation): void
    {
        if ($colocation->status === 'cancelled') {
            throw new AuthorizationException('This colocation is cancelled.');
        }
    }

    private function toCents(string $amount): int
    {
        $normalized = str_replace(',', '.', trim($amount));
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
