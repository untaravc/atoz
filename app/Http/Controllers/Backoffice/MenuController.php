<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return response()->json(Menu::query()->orderBy('sort_order')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'route' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['nullable', 'boolean'],
        ]);

        $menu = Menu::create($validated);

        return response()->json($menu, 201);
    }

    public function show(Menu $menu)
    {
        return response()->json($menu);
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'route' => ['sometimes', 'nullable', 'string', 'max:255'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:255'],
            'parent_id' => ['sometimes', 'nullable', 'integer'],
            'sort_order' => ['sometimes', 'nullable', 'integer'],
            'status' => ['sometimes', 'boolean'],
        ]);

        $menu->update($validated);

        return response()->json($menu);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return response()->json(['deleted' => true]);
    }
}
