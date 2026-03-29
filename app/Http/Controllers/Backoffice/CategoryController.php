<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::query()->latest()->paginate(15));
    }

    public function categoryList(Request $request)
    {
        $validated = $request->validate([
            'section' => ['required', 'string', 'in:transaction,transaction_status,price'],
        ]);

        return response()->json(
            Category::query()
                ->where('section', $validated['section'])
                ->orderBy('name')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:categories,code'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
            'section' => ['required', 'string', 'in:transaction,transaction_status,price'],
        ]);

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'code' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('categories', 'code')->ignore($category->id),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'section' => ['sometimes', 'string', 'in:transaction,transaction_status,price'],
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['deleted' => true]);
    }
}
