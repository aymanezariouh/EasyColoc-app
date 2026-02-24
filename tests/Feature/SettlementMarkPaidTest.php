<?php

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Membership;
use App\Models\Settlement;
use App\Models\User;
use App\Services\BalanceService;

function createColocationWithDebt(): array
{
    $owner = User::factory()->create(['name' => 'Owner']);
    $member = User::factory()->create(['name' => 'Member']);

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

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $owner->id,
        'title' => 'Rent',
        'amount' => 100.00,
        'expense_date' => '2026-03-01',
        'category_id' => null,
    ]);

    return [$colocation, $owner, $member];
}

it('mark paid creates a settlement record', function () {
    [$colocation, $owner, $member] = createColocationWithDebt();

    $response = $this->actingAs($member)
        ->post(route('settlements.mark-paid', $colocation), [
            'from_user_id' => $member->id,
            'to_user_id' => $owner->id,
            'amount' => '20.00',
        ]);

    $response->assertRedirect(route('colocations.show', $colocation));

    $this->assertDatabaseHas('settlements', [
        'colocation_id' => $colocation->id,
        'from_user_id' => $member->id,
        'to_user_id' => $owner->id,
        'amount' => 20.00,
    ]);
});

it('full payment removes remaining transfers', function () {
    [$colocation, $owner, $member] = createColocationWithDebt();

    $this->actingAs($member)
        ->post(route('settlements.mark-paid', $colocation), [
            'from_user_id' => $member->id,
            'to_user_id' => $owner->id,
            'amount' => '50.00',
        ])
        ->assertRedirect(route('colocations.show', $colocation));

    $transfers = app(BalanceService::class)->simplifiedTransfers($colocation);

    expect($transfers)->toHaveCount(0);
});

it('cannot pay more than owed', function () {
    [$colocation, $owner, $member] = createColocationWithDebt();

    $response = $this->actingAs($member)
        ->from(route('colocations.show', $colocation))
        ->post(route('settlements.mark-paid', $colocation), [
            'from_user_id' => $member->id,
            'to_user_id' => $owner->id,
            'amount' => '60.00',
        ]);

    $response->assertRedirect(route('colocations.show', $colocation))
        ->assertSessionHasErrors('amount');

    $this->assertDatabaseCount('settlements', 0);
});

it('non member cannot mark paid', function () {
    [$colocation, $owner, $member] = createColocationWithDebt();
    $outsider = User::factory()->create();

    $response = $this->actingAs($outsider)
        ->post(route('settlements.mark-paid', $colocation), [
            'from_user_id' => $member->id,
            'to_user_id' => $owner->id,
            'amount' => '10.00',
        ]);

    $response->assertForbidden();
    $this->assertDatabaseCount('settlements', 0);
});

it('cannot mark paid between users not in same colocation', function () {
    [$colocation, $owner, $member] = createColocationWithDebt();
    $outsider = User::factory()->create();

    $response = $this->actingAs($owner)
        ->from(route('colocations.show', $colocation))
        ->post(route('settlements.mark-paid', $colocation), [
            'from_user_id' => $outsider->id,
            'to_user_id' => $owner->id,
            'amount' => '10.00',
        ]);

    $response->assertRedirect(route('colocations.show', $colocation))
        ->assertSessionHasErrors('from_user_id');
});

it('settlements adjust balances correctly', function () {
    [$colocation, $owner, $member] = createColocationWithDebt();

    $this->actingAs($member)
        ->post(route('settlements.mark-paid', $colocation), [
            'from_user_id' => $member->id,
            'to_user_id' => $owner->id,
            'amount' => '20.00',
        ])
        ->assertRedirect(route('colocations.show', $colocation));

    $balances = app(BalanceService::class)->calculateBalances($colocation);
    $byUser = collect($balances)->keyBy(fn (array $row) => $row['user']->id);

    expect($byUser[$owner->id]['balance'])->toBe('30.00')
        ->and($byUser[$member->id]['balance'])->toBe('-30.00');

    $transfers = app(BalanceService::class)->simplifiedTransfers($colocation);

    expect($transfers)->toHaveCount(1)
        ->and($transfers[0]['from']->is($member))->toBeTrue()
        ->and($transfers[0]['to']->is($owner))->toBeTrue()
        ->and($transfers[0]['amount'])->toBe('30.00');
});
