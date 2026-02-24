<?php

use App\Models\Membership;
use App\Models\User;

it('authenticated user can view create colocation page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('colocations.create'))
        ->assertOk()
        ->assertSee('Create Colocation');
});

it('authenticated user can create colocation and becomes owner member', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('colocations.store'), [
            'name' => 'My New Colocation',
        ]);

    $membership = Membership::query()
        ->where('user_id', $user->id)
        ->where('role', 'owner')
        ->where('active', true)
        ->whereNull('left_at')
        ->first();

    expect($membership)->not->toBeNull();

    $response->assertRedirect(route('colocations.show', $membership->colocation_id));

    $this->assertDatabaseHas('colocations', [
        'id' => $membership->colocation_id,
        'name' => 'My New Colocation',
        'owner_id' => $user->id,
        'status' => 'active',
    ]);
});
