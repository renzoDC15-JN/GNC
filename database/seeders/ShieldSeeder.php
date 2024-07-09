<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_client::informations","view_any_client::informations","create_client::informations","update_client::informations","restore_client::informations","restore_any_client::informations","replicate_client::informations","reorder_client::informations","delete_client::informations","delete_any_client::informations","force_delete_client::informations","force_delete_any_client::informations","view_contact","view_any_contact","create_contact","update_contact","restore_contact","restore_any_contact","replicate_contact","reorder_contact","delete_contact","delete_any_contact","force_delete_contact","force_delete_any_contact","view_maintenance::companies","view_any_maintenance::companies","create_maintenance::companies","update_maintenance::companies","restore_maintenance::companies","restore_any_maintenance::companies","replicate_maintenance::companies","reorder_maintenance::companies","delete_maintenance::companies","delete_any_maintenance::companies","force_delete_maintenance::companies","force_delete_any_maintenance::companies","view_maintenance::documents","view_any_maintenance::documents","create_maintenance::documents","update_maintenance::documents","restore_maintenance::documents","restore_any_maintenance::documents","replicate_maintenance::documents","reorder_maintenance::documents","delete_maintenance::documents","delete_any_maintenance::documents","force_delete_maintenance::documents","force_delete_any_maintenance::documents","view_maintenance::locations","view_any_maintenance::locations","create_maintenance::locations","update_maintenance::locations","restore_maintenance::locations","restore_any_maintenance::locations","replicate_maintenance::locations","reorder_maintenance::locations","delete_maintenance::locations","delete_any_maintenance::locations","force_delete_maintenance::locations","force_delete_any_maintenance::locations","view_maintenance::projects","view_any_maintenance::projects","create_maintenance::projects","update_maintenance::projects","restore_maintenance::projects","restore_any_maintenance::projects","replicate_maintenance::projects","reorder_maintenance::projects","delete_maintenance::projects","delete_any_maintenance::projects","force_delete_maintenance::projects","force_delete_any_maintenance::projects","view_shield::role","view_any_shield::role","create_shield::role","update_shield::role","delete_shield::role","delete_any_shield::role","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","page_Themes"]},{"name":"panel_user","guard_name":"web","permissions":[]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
