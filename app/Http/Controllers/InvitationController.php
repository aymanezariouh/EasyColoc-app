<?php

namespace App\Http\Controllers;

use App\Exceptions\ActiveColocationExists;
use App\Exceptions\InvitationEmailMismatch;
use App\Exceptions\InvitationExpired;
use App\Exceptions\InvitationNotFound;
use App\Exceptions\InvitationNotPending;
use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function __construct(
        private readonly InvitationService $invitationService
    ) {
    }

    public function create(Request $request, Colocation $colocation): View
    {
        /** @var User $user */
        $user = $request->user();

        $isOwner = $colocation->memberships()
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->where('active', true)
            ->whereNull('left_at')
            ->exists();

        if (! $isOwner) {
            abort(403);
        }

        return view('invitations.create', [
            'colocation' => $colocation,
        ]);
    }

    public function store(Request $request, Colocation $colocation): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $this->invitationService->createInvitation($user, $colocation, $validated['email']);

        return redirect()
            ->route('colocations.show', $colocation)
            ->with('status', 'Invitation sent.');
    }

    public function show(string $token): View
    {
        $invitation = Invitation::with('colocation')
            ->where('token', $token)
            ->first();

        if (! $invitation) {
            abort(404);
        }

        return view('invitations.show', [
            'invitation' => $invitation,
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $this->invitationService->acceptInvitation($user, $token);

            return redirect()
                ->route('invitations.show', $token)
                ->with('status', 'Invitation accepted.');
        } catch (InvitationNotFound $e) {
            abort(404);
        } catch (InvitationExpired|InvitationEmailMismatch|InvitationNotPending|ActiveColocationExists $e) {
            return redirect()
                ->route('invitations.show', $token)
                ->withErrors(['invitation' => $e->getMessage()]);
        }
    }

    public function refuse(Request $request, string $token): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $this->invitationService->refuseInvitation($user, $token);

            return redirect()
                ->route('invitations.show', $token)
                ->with('status', 'Invitation refused.');
        } catch (InvitationNotFound $e) {
            abort(404);
        } catch (InvitationExpired|InvitationEmailMismatch|InvitationNotPending $e) {
            return redirect()
                ->route('invitations.show', $token)
                ->withErrors(['invitation' => $e->getMessage()]);
        }
    }
}
