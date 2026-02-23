<?php

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

it('applies default moderation and reputation fields on users', function () {
    $user = User::factory()->create();
    $stored = DB::table('users')->where('id', $user->id)->first();

    expect($stored)->not->toBeNull()
        ->and((int) $stored->is_admin)->toBe(0)
        ->and((int) $stored->is_banned)->toBe(0)
        ->and((int) $stored->reputation)->toBe(0);
});

it('enforces unique invitation token', function () {
    $owner = User::factory()->create();
    $colocationId = DB::table('colocations')->insertGetId([
        'name' => 'Test Colocation',
        'owner_id' => $owner->id,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('invitations')->insert([
        'colocation_id' => $colocationId,
        'email' => 'first@example.com',
        'token' => 'shared-token',
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $duplicateInsert = fn () => DB::table('invitations')->insert([
        'colocation_id' => $colocationId,
        'email' => 'second@example.com',
        'token' => 'shared-token',
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect($duplicateInsert)->toThrow(QueryException::class);
});
