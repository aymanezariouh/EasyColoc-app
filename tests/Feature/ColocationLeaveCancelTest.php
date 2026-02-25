<?php

use App\Models\Colocation;
use App\Models\Membership;
use App\Models\User;

function createColocationWithOwnerAndMember(): array
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

it('member can leave colocation', function () {
    [$colocation, $owner, $member] = createColocationWithOwnerAndMember();

    $response = $this->actingAs($member)
        ->post(route('colocations.leave', $colocation));

    $response->assertRedirect(route('dashboard'));
});

it('owner cannot leave colocation', function () {
    [$colocation, $owner, $member] = createColocationWithOwnerAndMember();

    $response = $this->actingAs($owner)
        ->post(route('colocations.leave', $colocation));

    $response->assertForbidden();
});

it('owner can cancel colocation', function () {
    [$colocation, $owner, $member] = createColocationWithOwnerAndMember();

    $response = $this->actingAs($owner)
        ->post(route('colocations.cancel', $colocation));

    $response->assertRedirect(route('dashboard'));
});

it('after cancel status is cancelled', function () {
    [$colocation, $owner, $member] = createColocationWithOwnerAndMember();

    $this->actingAs($owner)->post(route('colocations.cancel', $colocation));

    $colocation->refresh();

    expect($colocation->status)->toBe('cancelled')
        ->and($colocation->cancelled_at)->not->toBeNull();
});

it('after leave membership becomes inactive and left at is set', function () {
    [$colocation, $owner, $member] = createColocationWithOwnerAndMember();

    $this->actingAs($member)->post(route('colocations.leave', $colocation));

    $membership = Membership::where('user_id', $member->id)
        ->where('colocation_id', $colocation->id)
        ->first();

    expect($membership)->not->toBeNull()
        ->and($membership->active)->toBeFalse()
        ->and($membership->left_at)->not->toBeNull();
});

it('non member cannot leave or cancel', function () {
    [$colocation, $owner, $member] = createColocationWithOwnerAndMember();
    $outsider = User::factory()->create();

    $leaveResponse = $this->actingAs($outsider)
        ->post(route('colocations.leave', $colocation));

    $cancelResponse = $this->actingAs($outsider)
        ->post(route('colocations.cancel', $colocation));

    $leaveResponse->assertForbidden();
    $cancelResponse->assertForbidden();
});
