<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderBy('id')
            ->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        if ($actor && $actor->id === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['user' => 'You cannot ban yourself.']);
        }

        $user->forceFill([
            'is_banned' => true,
        ])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User banned.');
    }

    public function unban(User $user): RedirectResponse
    {
        $user->forceFill([
            'is_banned' => false,
        ])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User unbanned.');
    }
}
