<?php

namespace App\Services;

use App\Exceptions\ActiveColocationExists;
use App\Exceptions\InvitationEmailMismatch;
use App\Exceptions\InvitationExpired;
use App\Exceptions\InvitationNotFound;
use App\Exceptions\InvitationNotPending;
use App\Mail\ColocationInvitationMail;
use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationService
{
    /**
     * @throws AuthorizationException
     */
    public function createInvitation(User $owner, Colocation $colocation, string $email): Invitation
    {
        $ownerMembership = $colocation->memberships()
            ->where('user_id', $owner->id)
            ->where('role', 'owner')
            ->where('active', true)
            ->whereNull('left_at')
            ->exists();

        if (! $ownerMembership) {
            throw new AuthorizationException('Only the owner can invite users.');
        }

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => mb_strtolower(trim($email)),
            'token' => $this->generateUniqueToken(),
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
            'refused_at' => null,
        ]);

        Mail::to($invitation->email)->send(new ColocationInvitationMail($invitation));

        return $invitation;
    }

    /**
     * @throws ActiveColocationExists
     * @throws InvitationEmailMismatch
     * @throws InvitationExpired
     * @throws InvitationNotFound
     * @throws InvitationNotPending
     */
    public function acceptInvitation(User $user, string $token): Invitation
    {
        return DB::transaction(function () use ($user, $token): Invitation {
            $invitation = Invitation::where('token', $token)
                ->lockForUpdate()
                ->first();

            if (! $invitation) {
                throw new InvitationNotFound();
            }

            $this->assertPendingAndValidForUser($invitation, $user);

            $activeMembershipExists = Membership::query()
                ->where('user_id', $user->id)
                ->where('active', true)
                ->whereNull('left_at')
                ->exists();

            if ($activeMembershipExists) {
                throw new ActiveColocationExists();
            }

            Membership::create([
                'user_id' => $user->id,
                'colocation_id' => $invitation->colocation_id,
                'role' => 'member',
                'active' => true,
                'left_at' => null,
            ]);

            $invitation->forceFill([
                'status' => 'accepted',
                'accepted_at' => now(),
                'refused_at' => null,
            ])->save();

            return $invitation->refresh();
        });
    }

    /**
     * @throws InvitationEmailMismatch
     * @throws InvitationExpired
     * @throws InvitationNotFound
     * @throws InvitationNotPending
     */
    public function refuseInvitation(User $user, string $token): void
    {
        $invitation = Invitation::where('token', $token)->first();

        if (! $invitation) {
            throw new InvitationNotFound();
        }

        $this->assertPendingAndValidForUser($invitation, $user);

        $invitation->forceFill([
            'status' => 'refused',
            'refused_at' => now(),
            'accepted_at' => null,
        ])->save();
    }

    /**
     * @throws InvitationEmailMismatch
     * @throws InvitationExpired
     * @throws InvitationNotPending
     */
    private function assertPendingAndValidForUser(Invitation $invitation, User $user): void
    {
        if ($invitation->status !== 'pending') {
            throw new InvitationNotPending();
        }

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            throw new InvitationExpired();
        }

        if (mb_strtolower($user->email) !== mb_strtolower($invitation->email)) {
            throw new InvitationEmailMismatch();
        }
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while (Invitation::where('token', $token)->exists());

        return $token;
    }
}
