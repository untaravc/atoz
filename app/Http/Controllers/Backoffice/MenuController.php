<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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

    public function menu()
    {
        $menus = Menu::query()
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $grouped = $menus->groupBy('parent_id');

        $buildTree = function ($parentId) use (&$buildTree, $grouped) {
            return ($grouped->get($parentId, collect()))
                ->map(function ($menu) use (&$buildTree) {
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'route' => $menu->route,
                        'icon' => $menu->icon,
                        'parent_id' => $menu->parent_id,
                        'sort_order' => $menu->sort_order,
                        'status' => (bool) $menu->status,
                        'children' => $buildTree($menu->id),
                    ];
                })
                ->values();
        };

        $result = $buildTree(null);

        return response()->json([
            "status" => true,
            "result" => $result
        ]);
    }
}
