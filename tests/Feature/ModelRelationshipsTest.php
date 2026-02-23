<?php

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Invitation;
use App\Models\Membership;
use App\Models\Settlement;
use App\Models\User;

it('user can have memberships', function () {
    $user = User::factory()->create();
    $colocation = Colocation::factory()->create();

    $membership = Membership::factory()->create([
        'user_id' => $user->id,
        'colocation_id' => $colocation->id,
    ]);

    $user->load('memberships');

    expect($user->memberships)->toHaveCount(1)
        ->and($user->memberships->first()->is($membership))->toBeTrue();
});

it('colocation has owner and members', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $colocation = Colocation::factory()->create([
        'owner_id' => $owner->id,
    ]);

    Membership::factory()->create([
        'user_id' => $owner->id,
        'colocation_id' => $colocation->id,
        'role' => 'owner',
    ]);

    Membership::factory()->create([
        'user_id' => $member->id,
        'colocation_id' => $colocation->id,
        'role' => 'member',
    ]);

    $colocation->load('owner', 'members');

    expect($colocation->owner->is($owner))->toBeTrue()
        ->and($colocation->members->pluck('id')->all())->toContain($owner->id)
        ->and($colocation->members->pluck('id')->all())->toContain($member->id);
});

it('expense belongs to payer and colocation', function () {
    $payer = User::factory()->create();
    $colocation = Colocation::factory()->create();

    $expense = Expense::factory()->create([
        'payer_id' => $payer->id,
        'colocation_id' => $colocation->id,
        'category_id' => null,
    ]);

    $expense->load('payer', 'colocation');

    expect($expense->payer->is($payer))->toBeTrue()
        ->and($expense->colocation->is($colocation))->toBeTrue();
});

it('invitation belongs to colocation', function () {
    $colocation = Colocation::factory()->create();

    $invitation = Invitation::factory()->create([
        'colocation_id' => $colocation->id,
    ]);

    $invitation->load('colocation');

    expect($invitation->colocation->is($colocation))->toBeTrue();
});

it('settlement links from user and to user correctly', function () {
    $fromUser = User::factory()->create();
    $toUser = User::factory()->create();
    $colocation = Colocation::factory()->create();

    $settlement = Settlement::factory()->create([
        'colocation_id' => $colocation->id,
        'from_user_id' => $fromUser->id,
        'to_user_id' => $toUser->id,
    ]);

    $settlement->load('fromUser', 'toUser');

    expect($settlement->fromUser->is($fromUser))->toBeTrue()
        ->and($settlement->toUser->is($toUser))->toBeTrue();
});
