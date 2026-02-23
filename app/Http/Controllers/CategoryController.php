<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Colocation;
use App\Models\User;
use App\Services\CategoryService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    public function index(Request $request, Colocation $colocation): View
    {
        /** @var User $user */
        $user = $request->user();

        $isOwner = $colocation->memberships()
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->where('active', true)
            ->whereNull('left_at')
            ->exists();

        if (! $isOwner) {
            throw new AuthorizationException('Only owner can manage categories.');
        }

        $categories = $colocation->categories()->orderBy('name')->get();

        return view('categories.index', [
            'colocation' => $colocation,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Colocation $colocation): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $this->categoryService->createCategory($user, $colocation, $validated['name']);

        return redirect()
            ->route('categories.index', $colocation)
            ->with('status', 'Category created.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $this->categoryService->updateCategory($user, $category, $validated['name']);

        return redirect()
            ->route('categories.index', $category->colocation)
            ->with('status', 'Category updated.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->categoryService->deleteCategory($user, $category);

        return redirect()
            ->route('categories.index', $category->colocation)
            ->with('status', 'Category deleted.');
    }
}
