<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\User;
use App\Services\SettlementService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettlementController extends Controller
{
    public function __construct(
        private readonly SettlementService $settlementService
    ) {
    }

    public function markPaid(Request $request, Colocation $colocation): RedirectResponse
    {
        $validated = $request->validate([
            'from_user_id' => ['required', 'integer'],
            'to_user_id' => ['required', 'integer'],
            'amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
        ]);

        /** @var User $user */
        $user = $request->user();

        try {
            $this->settlementService->markPaid(
                $user,
                $colocation,
                (int) $validated['from_user_id'],
                (int) $validated['to_user_id'],
                (string) $validated['amount'],
            );

            return redirect()
                ->route('colocations.show', $colocation)
                ->with('status', 'Payment recorded.');
        } catch (AuthorizationException $e) {
            abort(403);
        } catch (ValidationException $e) {
            return redirect()
                ->route('colocations.show', $colocation)
                ->withErrors($e->errors());
        }
    }
}
