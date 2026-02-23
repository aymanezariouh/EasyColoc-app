<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('colocation_id')
                ->constrained('colocations')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('role', ['owner', 'member'])->default('member');
            $table->boolean('active')->default(true);
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'colocation_id']);
            $table->index(['colocation_id', 'active']);
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
