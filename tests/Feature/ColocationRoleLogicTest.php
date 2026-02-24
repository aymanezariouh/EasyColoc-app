<?php

use App\Models\Invitation;
use App\Models\Membership;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\ColocationService;
use App\Services\InvitationService;
use Illuminate\Auth\Access\AuthorizationException;

it('user who creates colocation has membership role owner', function () {
    $user = User::factory()->create();

    $colocation = app(ColocationService::class)->createColocation($user, 'Role Logic House');

    $membership = Membership::query()
        ->where('user_id', $user->id)
        ->where('colocation_id', $colocation->id)
        ->first();

    expect($colocation->owner_id)->toBe($user->id)
        ->and($membership)->not->toBeNull()
        ->and($membership->role)->toBe('owner')
        ->and($membership->active)->toBeTrue()
        ->and($membership->left_at)->toBeNull();
});

it('user who joins via invitation has membership role member', function () {
    $owner = User::factory()->create();
    $invitee = User::factory()->create([
        'email' => 'invitee@example.com',
    ]);

    $colocation = app(ColocationService::class)->createColocation($owner, 'Invitation Join House');

    $invitation = Invitation::factory()->create([
        'colocation_id' => $colocation->id,
        'email' => $invitee->email,
        'status' => 'pending',
        'expires_at' => now()->addDay(),
    ]);

    app(InvitationService::class)->acceptInvitation($invitee, $invitation->token);

    $membership = Membership::query()
        ->where('user_id', $invitee->id)
        ->where('colocation_id', $colocation->id)
        ->first();

    expect($membership)->not->toBeNull()
        ->and($membership->role)->toBe('member')
        ->and($membership->active)->toBeTrue()
        ->and($membership->left_at)->toBeNull();
});

it('owner permissions work only for role owner', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $colocation = app(ColocationService::class)->createColocation($owner, 'Permission House');

    Membership::factory()->create([
        'user_id' => $member->id,
        'colocation_id' => $colocation->id,
        'role' => 'member',
        'active' => true,
        'left_at' => null,
    ]);

    // owner_id remains a reference field; permission checks must use memberships.role.
    $colocation->forceFill(['owner_id' => $member->id])->save();

    $category = app(CategoryService::class)->createCategory($owner, $colocation, 'Food');

    expect($category->colocation_id)->toBe($colocation->id)
        ->and($category->name)->toBe('Food');
});

it('member cannot access owner only actions', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $colocation = app(ColocationService::class)->createColocation($owner, 'Restricted House');

    Membership::factory()->create([
        'user_id' => $member->id,
        'colocation_id' => $colocation->id,
        'role' => 'member',
        'active' => true,
        'left_at' => null,
    ]);

    app(CategoryService::class)->createCategory($member, $colocation, 'Should Fail');
})->throws(AuthorizationException::class);
