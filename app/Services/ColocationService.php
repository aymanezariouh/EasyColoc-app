<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class ColocationService
{
    /**
     * @throws AuthorizationException
     */
    public function leaveColocation(User $user, Colocation $colocation): void
    {
        DB::transaction(function () use ($user, $colocation): void {
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
            $ownerMembership = $colocation->memberships()
                ->where('user_id', $user->id)
                ->where('role', 'owner')
                ->where('active', true)
                ->whereNull('left_at')
                ->first();

            if (! $ownerMembership) {
                throw new AuthorizationException('Only the owner can cancel this colocation.');
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
}
