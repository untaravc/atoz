<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\MenuRole;
use Illuminate\Http\Request;

class MenuRoleController extends Controller
{
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
