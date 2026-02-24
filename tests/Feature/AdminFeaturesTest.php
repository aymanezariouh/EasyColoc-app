<?php

use App\Models\User;

it('first registered user becomes admin', function () {
    $this->post('/register', [
        'name' => 'First User',
        'email' => 'first@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $user = User::query()->where('email', 'first@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->is_admin)->toBeTrue();
});

it('non admin cannot access admin routes', function () {
    $admin = User::factory()->create();
    $nonAdmin = User::factory()->create();

    $this->actingAs($nonAdmin)
        ->get(route('admin.dashboard'))
        ->assertForbidden();

    $this->actingAs($nonAdmin)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

it('admin can access dashboard and users list', function () {
    $admin = User::factory()->create();
    User::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Admin Dashboard');

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Admin Users');
});

it('admin can ban and unban a user', function () {
    $admin = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.users.ban', $target))
        ->assertRedirect(route('admin.users.index'));

    $target->refresh();
    expect($target->is_banned)->toBeTrue();

    $this->actingAs($admin)
        ->post(route('admin.users.unban', $target))
        ->assertRedirect(route('admin.users.index'));

    $target->refresh();
    expect($target->is_banned)->toBeFalse();
});

it('admin cannot ban themselves', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->from(route('admin.users.index'))
        ->post(route('admin.users.ban', $admin))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHasErrors('user');

    $admin->refresh();
    expect($admin->is_banned)->toBeFalse();
});

it('banned user is blocked from auth routes and logged out', function () {
    $admin = User::factory()->create();
    $banned = User::factory()->create([
        'is_banned' => true,
    ]);

    $this->actingAs($banned)
        ->get(route('dashboard'))
        ->assertForbidden();

    $this->assertGuest();
});
