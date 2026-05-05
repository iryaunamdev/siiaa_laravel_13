<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Carga inicial de roles y permisos base del sistema.
     *
     * Se mantiene mínima en esta fase para evitar sobre-ingeniería.
     * Se ampliará por módulos en etapas posteriores.
     */
    public function run(): void
    {
        // Limpiar caché de permisos (requerido por Spatie)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ---- Permisos base ----
        $permissions = [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.update',
            'users.assign_roles',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ---- Roles base ----
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin      = Role::firstOrCreate(['name' => 'admin-sistema']);
        $usuario    = Role::firstOrCreate(['name' => 'usuario']);

        // ---- Asignación de permisos ----

        // super-admin: acceso total (se refuerza luego con Gate::before)
        $superAdmin->givePermissionTo(Permission::all());

        // admin-sistema: acceso a dashboard y gestión de usuarios
        $admin->givePermissionTo([
            'dashboard.view',
            'users.view',
            'users.create',
            'users.update',
            'users.assign_roles',
        ]);

        // usuario: acceso mínimo
        $usuario->givePermissionTo([
            'dashboard.view',
        ]);
    }
}
