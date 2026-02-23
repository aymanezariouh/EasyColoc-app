<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\User;
use App\Services\ColocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColocationController extends Controller
{
    public function __construct(
        private readonly ColocationService $colocationService
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

        return view('colocations.show', [
            'colocation' => $colocation,
            'membership' => $membership,
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
}
