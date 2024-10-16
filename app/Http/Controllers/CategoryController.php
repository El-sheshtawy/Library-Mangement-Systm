<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryGroupResource;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        Auth::loginUsingId(1); 

        $this->authorizeResource(Category::class, 'category');
    }

    public function index(Request $request)
    {
        $query = Category::with('categoryGroup');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhereHas('categoryGroup', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->filled('group_id')) {
            $query->where('category_group_id', $request->group_id);
        }

        $categories = $query->get();

        return CategoryResource::collection($categories);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'category_group_id' => 'required|exists:category_groups,id',
        ]);

        $category = Category::create($validated);
        return new CategoryResource($category);
    }

    public function show($id)
    {
        $category = Category::with('categoryGroup')->findOrFail($id);
        return new CategoryResource($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string|max:255',
            'category_group_id' => 'sometimes|exists:category_groups,id',
        ]);

        $category->update($validated);
        return new CategoryResource($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    public function categoryGroups(Request $request)
    {
        $query = CategoryGroup::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $categoryGroups = $query->get();
        return CategoryGroupResource::collection($categoryGroups);
    }

    public function showCategoryGroup($id)
    {
        $categoryGroup = CategoryGroup::with('category')->findOrFail($id);
        return new CategoryGroupResource($categoryGroup);
    }

    public function storeCategoryGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $categoryGroup = CategoryGroup::create($validated);
        return new CategoryGroupResource($categoryGroup);
    }

    public function updateCategoryGroup(Request $request, $id)
    {
        $categoryGroup = CategoryGroup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        $categoryGroup->update($validated);
        return new CategoryGroupResource($categoryGroup);
    }

    public function destroyCategoryGroup($id)
    {
        $categoryGroup = CategoryGroup::findOrFail($id);
        $categoryGroup->delete();
        return response()->json(['message' => 'Category group deleted successfully'], Response::HTTP_NO_CONTENT);
    }

}
