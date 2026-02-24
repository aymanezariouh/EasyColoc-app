<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();
    $activeMembership = $user?->memberships()
        ->with('colocation')
        ->where('active', true)
        ->whereNull('left_at')
        ->latest('id')
        ->first();

    $pendingInvitations = Invitation::query()
        ->with('colocation')
        ->whereRaw('LOWER(email) = ?', [mb_strtolower((string) $user?->email)])
        ->where('status', 'pending')
        ->where(function ($query): void {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        })
        ->latest('id')
        ->get();

    return view('dashboard', [
        'activeMembership' => $activeMembership,
        'pendingInvitations' => $pendingInvitations,
    ]);
})->middleware(['auth', 'verified', 'not_banned'])->name('dashboard');

Route::middleware(['auth', 'not_banned'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/colocations/create', [ColocationController::class, 'create'])->name('colocations.create');
    Route::post('/colocations', [ColocationController::class, 'store'])->name('colocations.store');
    Route::get('/colocations/{colocation}', [ColocationController::class, 'show'])->name('colocations.show');
    Route::post('/colocations/{colocation}/leave', [ColocationController::class, 'leave'])->name('colocations.leave');
    Route::post('/colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])->name('colocations.cancel');
    Route::post('/colocations/{colocation}/members/{user}/remove', [ColocationController::class, 'removeMember'])
        ->name('colocations.members.remove');

    Route::get('/colocations/{colocation}/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/colocations/{colocation}/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/{token}/refuse', [InvitationController::class, 'refuse'])->name('invitations.refuse');

    Route::get('/colocations/{colocation}/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/colocations/{colocation}/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::post('/colocations/{colocation}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    Route::post('/colocations/{colocation}/settlements/mark-paid', [SettlementController::class, 'markPaid'])
        ->name('settlements.mark-paid');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
        Route::post('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
    });
});

require __DIR__.'/auth.php';
