<?php

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Membership;
use App\Models\User;
use App\Services\BalanceService;

function activeMember(Colocation $colocation, User $user, string $role = 'member'): Membership
{
    return Membership::factory()->create([
        'user_id' => $user->id,
        'colocation_id' => $colocation->id,
        'role' => $role,
        'active' => true,
        'left_at' => null,
    ]);
}

it('calculates balances and transfers for two members', function () {
    $service = app(BalanceService::class);

    $userA = User::factory()->create(['name' => 'A']);
    $userB = User::factory()->create(['name' => 'B']);

    $colocation = Colocation::factory()->create([
        'owner_id' => $userA->id,
    ]);

    activeMember($colocation, $userA, 'owner');
    activeMember($colocation, $userB);

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $userA->id,
        'title' => 'Rent',
        'amount' => 100.00,
        'expense_date' => '2026-03-01',
        'category_id' => null,
    ]);

    $balances = $service->calculateBalances($colocation);
    $byUser = collect($balances)->keyBy(fn (array $row) => $row['user']->id);

    expect($byUser[$userA->id]['total_paid'])->toBe('100.00')
        ->and($byUser[$userA->id]['share'])->toBe('50.00')
        ->and($byUser[$userA->id]['balance'])->toBe('50.00')
        ->and($byUser[$userB->id]['total_paid'])->toBe('0.00')
        ->and($byUser[$userB->id]['share'])->toBe('50.00')
        ->and($byUser[$userB->id]['balance'])->toBe('-50.00');

    $transfers = $service->simplifiedTransfers($colocation);

    expect($transfers)->toHaveCount(1)
        ->and($transfers[0]['from']->is($userB))->toBeTrue()
        ->and($transfers[0]['to']->is($userA))->toBeTrue()
        ->and($transfers[0]['amount'])->toBe('50.00');
});

it('calculates balances and transfers for three members', function () {
    $service = app(BalanceService::class);

    $userA = User::factory()->create(['name' => 'A']);
    $userB = User::factory()->create(['name' => 'B']);
    $userC = User::factory()->create(['name' => 'C']);

    $colocation = Colocation::factory()->create([
        'owner_id' => $userA->id,
    ]);

    activeMember($colocation, $userA, 'owner');
    activeMember($colocation, $userB);
    activeMember($colocation, $userC);

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $userA->id,
        'title' => 'A Expense',
        'amount' => 90.00,
        'expense_date' => '2026-03-05',
        'category_id' => null,
    ]);

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $userB->id,
        'title' => 'B Expense',
        'amount' => 30.00,
        'expense_date' => '2026-03-06',
        'category_id' => null,
    ]);

    $balances = $service->calculateBalances($colocation);
    $byUser = collect($balances)->keyBy(fn (array $row) => $row['user']->id);

    expect($byUser[$userA->id]['total_paid'])->toBe('90.00')
        ->and($byUser[$userA->id]['share'])->toBe('40.00')
        ->and($byUser[$userA->id]['balance'])->toBe('50.00')
        ->and($byUser[$userB->id]['total_paid'])->toBe('30.00')
        ->and($byUser[$userB->id]['share'])->toBe('40.00')
        ->and($byUser[$userB->id]['balance'])->toBe('-10.00')
        ->and($byUser[$userC->id]['total_paid'])->toBe('0.00')
        ->and($byUser[$userC->id]['share'])->toBe('40.00')
        ->and($byUser[$userC->id]['balance'])->toBe('-40.00');

    $transfers = $service->simplifiedTransfers($colocation);

    expect($transfers)->toHaveCount(2)
        ->and($transfers[0]['from']->is($userB))->toBeTrue()
        ->and($transfers[0]['to']->is($userA))->toBeTrue()
        ->and($transfers[0]['amount'])->toBe('10.00')
        ->and($transfers[1]['from']->is($userC))->toBeTrue()
        ->and($transfers[1]['to']->is($userA))->toBeTrue()
        ->and($transfers[1]['amount'])->toBe('40.00');
});

it('includes expenses with null category in balances', function () {
    $service = app(BalanceService::class);

    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $colocation = Colocation::factory()->create([
        'owner_id' => $userA->id,
    ]);

    activeMember($colocation, $userA, 'owner');
    activeMember($colocation, $userB);

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $userA->id,
        'title' => 'Utility',
        'amount' => 20.00,
        'expense_date' => '2026-03-10',
        'category_id' => null,
    ]);

    $balances = $service->calculateBalances($colocation);
    $byUser = collect($balances)->keyBy(fn (array $row) => $row['user']->id);

    expect($byUser[$userA->id]['balance'])->toBe('10.00')
        ->and($byUser[$userB->id]['balance'])->toBe('-10.00');
});

it('excludes inactive or left members from balance calculations', function () {
    $service = app(BalanceService::class);

    $userA = User::factory()->create(['name' => 'A']);
    $userB = User::factory()->create(['name' => 'B']);
    $userC = User::factory()->create(['name' => 'C']);

    $colocation = Colocation::factory()->create([
        'owner_id' => $userA->id,
    ]);

    activeMember($colocation, $userA, 'owner');
    activeMember($colocation, $userB);

    Membership::factory()->create([
        'user_id' => $userC->id,
        'colocation_id' => $colocation->id,
        'role' => 'member',
        'active' => false,
        'left_at' => now(),
    ]);

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $userA->id,
        'title' => 'A Expense',
        'amount' => 60.00,
        'expense_date' => '2026-03-12',
        'category_id' => null,
    ]);

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $userC->id,
        'title' => 'C Expense',
        'amount' => 60.00,
        'expense_date' => '2026-03-13',
        'category_id' => null,
    ]);

    $balances = $service->calculateBalances($colocation);
    $byUser = collect($balances)->keyBy(fn (array $row) => $row['user']->id);

    expect($balances)->toHaveCount(2)
        ->and($byUser->has($userC->id))->toBeFalse()
        ->and($byUser[$userA->id]['share'])->toBe('30.00')
        ->and($byUser[$userB->id]['share'])->toBe('30.00')
        ->and($byUser[$userA->id]['balance'])->toBe('30.00')
        ->and($byUser[$userB->id]['balance'])->toBe('-30.00');

    $transfers = $service->simplifiedTransfers($colocation);

    expect($transfers)->toHaveCount(1)
        ->and($transfers[0]['from']->is($userB))->toBeTrue()
        ->and($transfers[0]['to']->is($userA))->toBeTrue()
        ->and($transfers[0]['amount'])->toBe('30.00');
});
