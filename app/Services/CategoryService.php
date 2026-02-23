<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Colocation;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function createCategory(User $user, Colocation $colocation, string $name): Category
    {
        $this->ensureOwner($user, $colocation);
        $normalizedName = trim($name);

        $exists = $colocation->categories()
            ->where('name', $normalizedName)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'name' => 'Category already exists in this colocation.',
            ]);
        }

        return DB::transaction(function () use ($colocation, $normalizedName): Category {
            return Category::create([
                'colocation_id' => $colocation->id,
                'name' => $normalizedName,
            ]);
        });
    }

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function updateCategory(User $user, Category $category, string $name): Category
    {
        $this->ensureOwner($user, $category->colocation);
        $normalizedName = trim($name);

        $exists = $category->colocation->categories()
            ->where('name', $normalizedName)
            ->whereKeyNot($category->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'name' => 'Category already exists in this colocation.',
            ]);
        }

        return DB::transaction(function () use ($category, $normalizedName): Category {
            $category->forceFill([
                'name' => $normalizedName,
            ])->save();

            return $category;
        });
    }

    /**
     * @throws AuthorizationException
     */
    public function deleteCategory(User $user, Category $category): void
    {
        $this->ensureOwner($user, $category->colocation);

        DB::transaction(function () use ($category): void {
            $category->delete();
        });
    }

    /**
     * @throws AuthorizationException
     */
    private function ensureOwner(User $user, Colocation $colocation): void
    {
        $isOwner = $colocation->memberships()
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->where('active', true)
            ->whereNull('left_at')
            ->exists();

        if (! $isOwner) {
            throw new AuthorizationException('Only owner can manage categories.');
        }
    }
}
