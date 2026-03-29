<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuRole;
use App\Models\Role;
use Illuminate\Http\Request;

class MenuRoleController extends Controller
{
    public function permissions(Request $request)
    {
        $validated = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $role = Role::query()->findOrFail($validated['role_id']);

        $menus = Menu::query()
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'route', 'icon', 'parent_id', 'sort_order', 'status']);

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

        $tree = $buildTree(null);

        $rows = MenuRole::query()
            ->where('role_id', $role->id)
            ->get(['menu_id', 'method']);

        $permissions = $rows
            ->groupBy('menu_id')
            ->map(fn ($items) => $items->pluck('method')->values())
            ->toArray();

        return response()->json([
            'role' => $role,
            'menus' => $tree,
            'methods' => ['get', 'post', 'patch', 'delete'],
            'permissions' => $permissions,
        ]);
    }

    public function updatePermissions(Request $request)
    {
        $validated = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
            'permissions.*.method' => ['required', 'string', 'in:get,post,patch,delete'],
        ]);

        $roleId = $validated['role_id'];
        $permissions = collect($validated['permissions'] ?? [])
            ->map(fn ($item) => [
                'menu_id' => (int) $item['menu_id'],
                'method' => $item['method'],
            ])
            ->unique(fn ($item) => $item['menu_id'] . '|' . $item['method'])
            ->values();

        MenuRole::query()->where('role_id', $roleId)->delete();

        $permissions->each(function ($permission) use ($roleId) {
            MenuRole::create([
                'role_id' => $roleId,
                'menu_id' => $permission['menu_id'],
                'method' => $permission['method'],
            ]);
        });

        return response()->json([
            'updated' => true,
            'count' => $permissions->count(),
        ]);
    }

    public function index()
    {
        return response()->json(MenuRole::query()->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => ['required', 'integer'],
            'role_id' => ['required', 'integer'],
            'method' => ['required', 'string', 'max:10'],
        ]);

        $menuRole = MenuRole::create($validated);

        return response()->json($menuRole, 201);
    }

    public function show(MenuRole $menuRole)
    {
        return response()->json($menuRole);
    }

    public function update(Request $request, MenuRole $menuRole)
    {
        $validated = $request->validate([
            'menu_id' => ['sometimes', 'integer'],
            'role_id' => ['sometimes', 'integer'],
            'method' => ['sometimes', 'string', 'max:10'],
        ]);

        $menuRole->update($validated);

        return response()->json($menuRole);
    }

    public function destroy(MenuRole $menuRole)
    {
        $menuRole->delete();

        return response()->json(['deleted' => true]);
    }
}
