<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Membership;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Global Admin',
            'email' => 'admin@easycoloc.test',
            'password' => 'password',
            'is_admin' => true,
            'is_banned' => false,
            'reputation' => 0,
        ]);

        $owner = User::factory()->create([
            'name' => 'Olivia Owner',
            'email' => 'owner@easycoloc.test',
            'password' => 'password',
            'is_admin' => false,
            'is_banned' => false,
            'reputation' => 0,
        ]);

        $memberOne = User::factory()->create([
            'name' => 'Mia Member',
            'email' => 'member1@easycoloc.test',
            'password' => 'password',
            'is_admin' => false,
            'is_banned' => false,
            'reputation' => 0,
        ]);

        $memberTwo = User::factory()->create([
            'name' => 'Noah Member',
            'email' => 'member2@easycoloc.test',
            'password' => 'password',
            'is_admin' => false,
            'is_banned' => false,
            'reputation' => 0,
        ]);

        User::factory()->create([
            'name' => 'Invitee Test',
            'email' => 'invitee.test@easycoloc.test',
            'password' => 'password',
            'is_admin' => false,
            'is_banned' => false,
            'reputation' => 0,
        ]);

        $colocation = Colocation::create([
            'name' => 'Demo House',
            'owner_id' => $owner->id,
            'status' => 'active',
            'cancelled_at' => null,
        ]);

        Membership::create([
            'user_id' => $owner->id,
            'colocation_id' => $colocation->id,
            'role' => 'owner',
            'active' => true,
            'left_at' => null,
        ]);

        Membership::create([
            'user_id' => $memberOne->id,
            'colocation_id' => $colocation->id,
            'role' => 'member',
            'active' => true,
            'left_at' => null,
        ]);

        Membership::create([
            'user_id' => $memberTwo->id,
            'colocation_id' => $colocation->id,
            'role' => 'member',
            'active' => true,
            'left_at' => null,
        ]);

        $housing = Category::create([
            'colocation_id' => $colocation->id,
            'name' => 'Housing',
        ]);

        $food = Category::create([
            'colocation_id' => $colocation->id,
            'name' => 'Food',
        ]);

        $utilities = Category::create([
            'colocation_id' => $colocation->id,
            'name' => 'Utilities',
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'category_id' => $housing->id,
            'payer_id' => $owner->id,
            'title' => 'March Rent',
            'amount' => 120.00,
            'expense_date' => '2026-03-01',
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'category_id' => $food->id,
            'payer_id' => $memberOne->id,
            'title' => 'March Groceries',
            'amount' => 40.00,
            'expense_date' => '2026-03-12',
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'category_id' => $utilities->id,
            'payer_id' => $owner->id,
            'title' => 'April Internet',
            'amount' => 60.00,
            'expense_date' => '2026-04-03',
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'category_id' => $utilities->id,
            'payer_id' => $memberTwo->id,
            'title' => 'April Electricity',
            'amount' => 20.00,
            'expense_date' => '2026-04-10',
        ]);

        Settlement::create([
            'colocation_id' => $colocation->id,
            'from_user_id' => $memberTwo->id,
            'to_user_id' => $owner->id,
            'amount' => 25.00,
            'paid_at' => now(),
        ]);
    }
}
