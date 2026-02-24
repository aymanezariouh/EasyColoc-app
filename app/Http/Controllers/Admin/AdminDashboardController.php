<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalUsers' => User::query()->count(),
            'totalColocations' => Colocation::query()->count(),
            'totalExpenses' => Expense::query()->count(),
            'totalBannedUsers' => User::query()->where('is_banned', true)->count(),
            'totalActiveColocations' => Colocation::query()->where('status', 'active')->count(),
        ]);
    }
}
