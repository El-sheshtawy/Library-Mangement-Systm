<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryGroupResource;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Simulate user login for testing purposes
        // Remove or modify this in production
        if (app()->environment('local')) {
            Auth::loginUsingId(1);
        }
        $this->authorizeResource(Category::class, 'category');
    }

    // Category CRUD operations

    public function index(Request $request)
    {
        $query = Category::with('categoryGroup');

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('name', 'like', $searchTerm)
                ->orWhereHas('categoryGroup', fn($q) => $q->where('name', 'like', $searchTerm));
        }

        if ($request->filled('group_id')) {
            $query->where('category_group_id', $request->group_id);
        }

        return CategoryResource::collection($query->get());
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);

        $category = Category::create($validated);
        return new CategoryResource($category);
    }

    public function show($id)
    {
        $category = $this->findCategory($id);
        return new CategoryResource($category);
    }

    public function update(Request $request, $id)
    {
        $category = $this->findCategory($id);

        $validated = $this->validateCategory($request, 'update');
        $category->update($validated);

        return new CategoryResource($category);
    }

    public function destroy($id)
    {
        $category = $this->findCategory($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    // Category Group CRUD operations

    public function categoryGroups(Request $request)
    {
        $query = CategoryGroup::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return CategoryGroupResource::collection($query->get());
    }

    public function showCategoryGroup($id)
    {
        $categoryGroup = $this->findCategoryGroup($id);
        return new CategoryGroupResource($categoryGroup);
    }

    public function storeCategoryGroup(Request $request)
    {
        $validated = $this->validateCategoryGroup($request);

        $categoryGroup = CategoryGroup::create($validated);
        return new CategoryGroupResource($categoryGroup);
    }

    public function updateCategoryGroup(Request $request, $id)
    {
        $categoryGroup = $this->findCategoryGroup($id);

        $validated = $this->validateCategoryGroup($request, 'update');
        $categoryGroup->update($validated);

        return new CategoryGroupResource($categoryGroup);
    }

    public function destroyCategoryGroup($id)
    {
        $categoryGroup = $this->findCategoryGroup($id);
        $categoryGroup->delete();

        return response()->json(['message' => 'Category group deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    // Private helper methods

    /**
     * Validate Category data.
     */
    private function validateCategory(Request $request, $action = 'create')
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'category_group_id' => 'required|exists:category_groups,id',
        ];

        if ($action === 'update') {
            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string|max:255',
                'category_group_id' => 'sometimes|exists:category_groups,id',
            ];
        }

        return $request->validate($rules);
    }

    /**
     * Validate Category Group data.
     */
    private function validateCategoryGroup(Request $request, $action = 'create')
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];

        if ($action === 'update') {
            $rules = ['name' => 'sometimes|required|string|max:255'];
        }

        return $request->validate($rules);
    }

    /**
     * Find Category by ID or throw an exception.
     */
    private function findCategory($id)
    {
        return Category::with('categoryGroup')->findOrFail($id);
    }

    /**
     * Find Category Group by ID or throw an exception.
     */
    private function findCategoryGroup($id)
    {
        return CategoryGroup::with('category')->findOrFail($id);
    }
}
