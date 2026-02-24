<?php

use App\Mail\ColocationInvitationMail;
use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function createColocationForInvitationFlow(): array
{
    $owner = User::factory()->create();
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

    return [$owner, $colocation];
}

it('owner can create invitation and token is stored', function () {
    Mail::fake();
    [$owner, $colocation] = createColocationForInvitationFlow();

    $response = $this->actingAs($owner)
        ->post(route('invitations.store', $colocation), [
            'email' => 'invitee@example.com',
        ]);

    $response->assertRedirect(route('colocations.show', $colocation));

    $invitation = Invitation::where('email', 'invitee@example.com')->first();

    expect($invitation)->not->toBeNull()
        ->and($invitation->status)->toBe('pending')
        ->and($invitation->token)->not->toBeEmpty();

    Mail::assertSent(ColocationInvitationMail::class, function (ColocationInvitationMail $mail) use ($invitation) {
        return $mail->invitation->is($invitation);
    });
});

it('non owner cannot create invitation', function () {
    Mail::fake();
    [$owner, $colocation] = createColocationForInvitationFlow();
    $member = User::factory()->create();

    Membership::factory()->create([
        'user_id' => $member->id,
        'colocation_id' => $colocation->id,
        'role' => 'member',
        'active' => true,
        'left_at' => null,
    ]);

    $response = $this->actingAs($member)
        ->post(route('invitations.store', $colocation), [
            'email' => 'invitee@example.com',
        ]);

    $response->assertForbidden();
});

it('accept works and creates membership', function () {
    [$owner, $colocation] = createColocationForInvitationFlow();
    $user = User::factory()->create([
        'email' => 'invitee@example.com',
    ]);

    $invitation = Invitation::factory()->create([
        'colocation_id' => $colocation->id,
        'email' => $user->email,
        'status' => 'pending',
        'expires_at' => now()->addDay(),
    ]);

    $response = $this->actingAs($user)
        ->post(route('invitations.accept', $invitation->token));

    $response->assertRedirect(route('colocations.show', $colocation));

    $invitation->refresh();

    expect($invitation->status)->toBe('accepted')
        ->and($invitation->accepted_at)->not->toBeNull();

    $this->assertDatabaseHas('memberships', [
        'user_id' => $user->id,
        'colocation_id' => $colocation->id,
        'role' => 'member',
        'active' => true,
    ]);
});

it('accept fails if user has active colocation', function () {
    [$owner, $colocation] = createColocationForInvitationFlow();
    $user = User::factory()->create([
        'email' => 'invitee@example.com',
    ]);

    $otherColocation = Colocation::factory()->create();
    Membership::factory()->create([
        'user_id' => $user->id,
        'colocation_id' => $otherColocation->id,
        'role' => 'member',
        'active' => true,
        'left_at' => null,
    ]);

    $invitation = Invitation::factory()->create([
        'colocation_id' => $colocation->id,
        'email' => $user->email,
        'status' => 'pending',
        'expires_at' => now()->addDay(),
    ]);

    $response = $this->actingAs($user)
        ->from(route('invitations.show', $invitation->token))
        ->post(route('invitations.accept', $invitation->token));

    $response->assertRedirect(route('invitations.show', $invitation->token))
        ->assertSessionHasErrors('invitation');

    $this->assertDatabaseMissing('memberships', [
        'user_id' => $user->id,
        'colocation_id' => $colocation->id,
        'role' => 'member',
        'active' => true,
    ]);
});

it('accept fails if email mismatch', function () {
    [$owner, $colocation] = createColocationForInvitationFlow();
    $user = User::factory()->create([
        'email' => 'another@example.com',
    ]);

    $invitation = Invitation::factory()->create([
        'colocation_id' => $colocation->id,
        'email' => 'invitee@example.com',
        'status' => 'pending',
        'expires_at' => now()->addDay(),
    ]);

    $response = $this->actingAs($user)
        ->post(route('invitations.accept', $invitation->token));

    $response->assertRedirect(route('invitations.show', $invitation->token))
        ->assertSessionHasErrors('invitation');
});

it('accept fails if expired', function () {
    [$owner, $colocation] = createColocationForInvitationFlow();
    $user = User::factory()->create([
        'email' => 'invitee@example.com',
    ]);

    $invitation = Invitation::factory()->create([
        'colocation_id' => $colocation->id,
        'email' => 'invitee@example.com',
        'status' => 'pending',
        'expires_at' => now()->subMinute(),
    ]);

    $response = $this->actingAs($user)
        ->post(route('invitations.accept', $invitation->token));

    $response->assertRedirect(route('invitations.show', $invitation->token))
        ->assertSessionHasErrors('invitation');
});

it('refuse marks invitation refused', function () {
    [$owner, $colocation] = createColocationForInvitationFlow();
    $user = User::factory()->create([
        'email' => 'invitee@example.com',
    ]);

    $invitation = Invitation::factory()->create([
        'colocation_id' => $colocation->id,
        'email' => 'invitee@example.com',
        'status' => 'pending',
        'expires_at' => now()->addDay(),
    ]);

    $response = $this->actingAs($user)
        ->post(route('invitations.refuse', $invitation->token));

    $response->assertRedirect(route('invitations.show', $invitation->token));

    $invitation->refresh();

    expect($invitation->status)->toBe('refused')
        ->and($invitation->refused_at)->not->toBeNull();
});

it('accept or refuse fails if invitation is already processed', function () {
    [$owner, $colocation] = createColocationForInvitationFlow();
    $user = User::factory()->create([
        'email' => 'invitee@example.com',
    ]);

    $acceptedInvitation = Invitation::factory()->create([
        'colocation_id' => $colocation->id,
        'email' => 'invitee@example.com',
        'status' => 'accepted',
        'accepted_at' => now(),
    ]);

    $refusedInvitation = Invitation::factory()->create([
        'colocation_id' => $colocation->id,
        'email' => 'invitee@example.com',
        'status' => 'refused',
        'refused_at' => now(),
    ]);

    $acceptResponse = $this->actingAs($user)
        ->post(route('invitations.accept', $acceptedInvitation->token));

    $refuseResponse = $this->actingAs($user)
        ->post(route('invitations.refuse', $refusedInvitation->token));

    $acceptResponse->assertRedirect(route('invitations.show', $acceptedInvitation->token))
        ->assertSessionHasErrors('invitation');

    $refuseResponse->assertRedirect(route('invitations.show', $refusedInvitation->token))
        ->assertSessionHasErrors('invitation');
});
