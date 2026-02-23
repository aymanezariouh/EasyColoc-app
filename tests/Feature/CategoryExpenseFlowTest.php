<?php

use App\Models\Category;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Membership;
use App\Models\User;

function createColocationOwnerAndMember(): array
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

it('owner can create category and member cannot', function () {
    [$colocation, $owner, $member] = createColocationOwnerAndMember();

    $ownerResponse = $this->actingAs($owner)
        ->post(route('categories.store', $colocation), [
            'name' => 'Food',
        ]);

    $ownerResponse->assertRedirect(route('categories.index', $colocation));

    $this->assertDatabaseHas('categories', [
        'colocation_id' => $colocation->id,
        'name' => 'Food',
    ]);

    $memberResponse = $this->actingAs($member)
        ->post(route('categories.store', $colocation), [
            'name' => 'Transport',
        ]);

    $memberResponse->assertForbidden();
});

it('category name is unique per colocation', function () {
    [$colocation, $owner, $member] = createColocationOwnerAndMember();

    $this->actingAs($owner)
        ->post(route('categories.store', $colocation), [
            'name' => 'Food',
        ]);

    $response = $this->actingAs($owner)
        ->from(route('categories.index', $colocation))
        ->post(route('categories.store', $colocation), [
            'name' => 'Food',
        ]);

    $response->assertRedirect(route('categories.index', $colocation))
        ->assertSessionHasErrors('name');

    expect(Category::where('colocation_id', $colocation->id)
        ->where('name', 'Food')
        ->count())->toBe(1);
});

it('member can add expense in their colocation', function () {
    [$colocation, $owner, $member] = createColocationOwnerAndMember();

    $response = $this->actingAs($member)
        ->post(route('expenses.store', $colocation), [
            'title' => 'Internet Bill',
            'amount' => 35.50,
            'expense_date' => '2026-02-12',
            'category_id' => null,
        ]);

    $response->assertRedirect(route('colocations.show', $colocation));

    $exists = Expense::query()
        ->where('colocation_id', $colocation->id)
        ->where('payer_id', $member->id)
        ->where('title', 'Internet Bill')
        ->where('amount', 35.50)
        ->whereDate('expense_date', '2026-02-12')
        ->exists();

    expect($exists)->toBeTrue();
});

it('non member cannot add expense', function () {
    [$colocation, $owner, $member] = createColocationOwnerAndMember();
    $outsider = User::factory()->create();

    $response = $this->actingAs($outsider)
        ->post(route('expenses.store', $colocation), [
            'title' => 'Gas',
            'amount' => 20.00,
            'expense_date' => '2026-02-12',
            'category_id' => null,
        ]);

    $response->assertForbidden();
});

it('month filter returns only expenses in that month', function () {
    [$colocation, $owner, $member] = createColocationOwnerAndMember();

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $owner->id,
        'title' => 'January Expense',
        'amount' => 12.00,
        'expense_date' => '2026-01-15',
    ]);

    Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $owner->id,
        'title' => 'February Expense',
        'amount' => 30.00,
        'expense_date' => '2026-02-20',
    ]);

    $response = $this->actingAs($owner)
        ->get(route('colocations.show', [
            'colocation' => $colocation,
            'month' => '2026-02',
        ]));

    $response->assertOk()
        ->assertSee('February Expense')
        ->assertDontSee('January Expense');
});

it('delete expense authorization works for payer only', function () {
    [$colocation, $owner, $member] = createColocationOwnerAndMember();

    $expense = Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $member->id,
        'title' => 'Delete Me',
        'amount' => 11.00,
        'expense_date' => '2026-02-10',
    ]);

    $payerResponse = $this->actingAs($member)
        ->delete(route('expenses.destroy', $expense));

    $payerResponse->assertRedirect(route('colocations.show', $colocation));
    $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);

    $expenseTwo = Expense::factory()->create([
        'colocation_id' => $colocation->id,
        'payer_id' => $member->id,
        'title' => 'Keep Me',
        'amount' => 18.00,
        'expense_date' => '2026-02-11',
    ]);

    $nonPayerResponse = $this->actingAs($owner)
        ->delete(route('expenses.destroy', $expenseTwo));

    $nonPayerResponse->assertForbidden();
    $this->assertDatabaseHas('expenses', ['id' => $expenseTwo->id]);
});

it('expense can have null category', function () {
    [$colocation, $owner, $member] = createColocationOwnerAndMember();

    $response = $this->actingAs($member)
        ->post(route('expenses.store', $colocation), [
            'title' => 'No Category Expense',
            'amount' => 9.50,
            'expense_date' => '2026-02-21',
            'category_id' => null,
        ]);

    $response->assertRedirect(route('colocations.show', $colocation));

    $exists = Expense::query()
        ->where('colocation_id', $colocation->id)
        ->where('payer_id', $member->id)
        ->where('title', 'No Category Expense')
        ->whereNull('category_id')
        ->whereDate('expense_date', '2026-02-21')
        ->exists();

    expect($exists)->toBeTrue();
});
