<?php

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Membership;
use App\Models\User;
use App\Services\BalanceService;

function reputationFixtureTwoMembers(): array
{
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $colocation = Colocation::factory()->create([
        'owner_id' => $owner->id,
    ]);

    Membership::factory()->create([
        'user_id' => $owner->id,
        'colocation_id' => $colocation->id,
        'role' => 'owner',
        'active' => true,
        'left_at' => null,
    ]);

    Membership::factory()->create([
        'user_id' => $member->id,
        'colocation_id' => $colocation->id,
        'role' => 'member',
        'active' => true,
        'left_at' => null,
    ]);

    return [$colocation, $owner, $member];
}

it('member leaves with debt and loses reputation', function () {
    [$colocation, $owner, $member] = reputationFixtureTwoMembers();

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $owner->id,
        'title' => 'Rent',
        'amount' => 100.00,
        'expense_date' => '2026-03-01',
        'category_id' => null,
    ]);

    $this->actingAs($member)
        ->post(route('colocations.leave', $colocation))
        ->assertRedirect(route('dashboard'));

    $member->refresh();

    $membership = Membership::query()
        ->where('colocation_id', $colocation->id)
        ->where('user_id', $member->id)
        ->first();

    expect($member->reputation)->toBe(-1)
        ->and($membership->active)->toBeFalse()
        ->and($membership->left_at)->not->toBeNull();
});

it('member leaves with non negative balance and gains reputation', function () {
    [$colocation, $owner, $member] = reputationFixtureTwoMembers();

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $member->id,
        'title' => 'Groceries',
        'amount' => 100.00,
        'expense_date' => '2026-03-02',
        'category_id' => null,
    ]);

    $this->actingAs($member)
        ->post(route('colocations.leave', $colocation))
        ->assertRedirect(route('dashboard'));

    $member->refresh();

    expect($member->reputation)->toBe(1);
});

it('owner cancel updates all active members reputations from balances', function () {
    [$colocation, $owner, $memberA] = reputationFixtureTwoMembers();
    $memberB = User::factory()->create();

    Membership::factory()->create([
        'user_id' => $memberB->id,
        'colocation_id' => $colocation->id,
        'role' => 'member',
        'active' => true,
        'left_at' => null,
    ]);

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $owner->id,
        'title' => 'Owner Expense',
        'amount' => 90.00,
        'expense_date' => '2026-03-05',
        'category_id' => null,
    ]);

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $memberA->id,
        'title' => 'Member Expense',
        'amount' => 30.00,
        'expense_date' => '2026-03-06',
        'category_id' => null,
    ]);

    $this->actingAs($owner)
        ->post(route('colocations.cancel', $colocation))
        ->assertRedirect(route('dashboard'));

    $owner->refresh();
    $memberA->refresh();
    $memberB->refresh();
    $colocation->refresh();

    expect($owner->reputation)->toBe(1)
        ->and($memberA->reputation)->toBe(-1)
        ->and($memberB->reputation)->toBe(-1)
        ->and($colocation->status)->toBe('cancelled');
});

it('owner removes member with debt and imputes debt to owner', function () {
    [$colocation, $owner, $member] = reputationFixtureTwoMembers();

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $owner->id,
        'title' => 'Rent',
        'amount' => 100.00,
        'expense_date' => '2026-03-01',
        'category_id' => null,
    ]);

    $this->actingAs($owner)
        ->post(route('colocations.members.remove', [
            'colocation' => $colocation,
            'user' => $member,
        ]))
        ->assertRedirect(route('colocations.show', $colocation));

    $this->assertDatabaseHas('settlements', [
        'colocation_id' => $colocation->id,
        'from_user_id' => $owner->id,
        'to_user_id' => $member->id,
        'amount' => 50.00,
    ]);

    $member->refresh();

    $memberMembership = Membership::query()
        ->where('colocation_id', $colocation->id)
        ->where('user_id', $member->id)
        ->first();

    expect($member->reputation)->toBe(-1)
        ->and($memberMembership->active)->toBeFalse()
        ->and($memberMembership->left_at)->not->toBeNull();

    $balances = app(BalanceService::class)->calculateBalances($colocation);
    $balancesById = collect($balances)->keyBy(fn (array $row) => $row['user']->id);

    expect($balancesById->has($member->id))->toBeFalse()
        ->and($balancesById[$owner->id]['balance'])->toBe('-50.00');
});

it('owner cannot remove themselves or another owner', function () {
    [$colocation, $owner, $member] = reputationFixtureTwoMembers();
    $anotherOwner = User::factory()->create();

    Membership::factory()->create([
        'user_id' => $anotherOwner->id,
        'colocation_id' => $colocation->id,
        'role' => 'owner',
        'active' => true,
        'left_at' => null,
    ]);

    $this->actingAs($owner)
        ->post(route('colocations.members.remove', [
            'colocation' => $colocation,
            'user' => $owner,
        ]))
        ->assertForbidden();

    $this->actingAs($owner)
        ->post(route('colocations.members.remove', [
            'colocation' => $colocation,
            'user' => $anotherOwner,
        ]))
        ->assertForbidden();
});
