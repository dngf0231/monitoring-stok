<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrator role with full access to all features',
                'permissions' => collect(Role::AVAILABLE_PERMISSIONS)
                    ->flatMap(fn ($permissions) => array_keys($permissions))
                    ->values()
                    ->all(),
            ],
            [
                'name' => 'user',
                'description' => 'User role with basic access to inventory features',
                'permissions' => [
                    'dashboard.view',
                    'barang.view',
                    'barang_keluar.view',
                    'barang_keluar.create',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                [
                    'description' => $roleData['description'],
                    'permissions' => $roleData['permissions'],
                ]
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}
