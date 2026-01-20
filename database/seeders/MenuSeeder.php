<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $dashboard = Menu::updateOrCreate(
            ['route' => '/bo/dashboard'],
            [
                'name' => 'Dashboard',
                'icon' => 'fa-chart-pie',
                'parent_id' => null,
                'sort_order' => 1,
                'status' => true,
            ]
        );

        $setting = Menu::updateOrCreate(
            ['name' => 'Setting', 'parent_id' => null],
            [
                'route' => null,
                'icon' => 'fa-cog',
                'sort_order' => 2,
                'status' => true,
            ]
        );

        Menu::updateOrCreate(
            ['route' => '/bo/roles'],
            [
                'name' => 'Role',
                'icon' => 'fa-users',
                'parent_id' => $setting->id,
                'sort_order' => 1,
                'status' => true,
            ]
        );

        Menu::updateOrCreate(
            ['route' => '/bo/users'],
            [
                'name' => 'User',
                'icon' => 'fa-users',
                'parent_id' => $setting->id,
                'sort_order' => 2,
                'status' => true,
            ]
        );

        Menu::updateOrCreate(
            ['route' => '/bo/menus'],
            [
                'name' => 'Menu',
                'icon' => 'fa-file-alt',
                'parent_id' => $setting->id,
                'sort_order' => 3,
                'status' => true,
            ]
        );

        Menu::updateOrCreate(
            ['route' => '/bo/menu-role'],
            [
                'name' => 'Menu Role',
                'icon' => 'fa-file-alt',
                'parent_id' => $setting->id,
                'sort_order' => 4,
                'status' => true,
            ]
        );
    }
}
