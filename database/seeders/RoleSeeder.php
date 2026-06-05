<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator'],
            ['name' => 'head-office', 'display_name' => 'Head Office'],
            ['name' => 'area-manager', 'display_name' => 'Area Manager'],
            ['name' => 'marketing-officer', 'display_name' => 'Marketing Officer'],
            ['name' => 'outlet-owner', 'display_name' => 'Outlet Owner'],
            ['name' => 'rider', 'display_name' => 'Delivery Rider'],
        ];
        
        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['display_name' => $role['display_name']]
            );
        }
    }
}