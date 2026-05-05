<?php

namespace App\Livewire\Sys;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsMatrix extends Component
{
    public $roles = [];
    public $permissions = [];
    public $matrix = [];

    public $canEdit = false;
    public $groupedPermissions = [];

    public function mount()
    {
        $this->roles = Role::orderBy('name')->get();
        $this->permissions = Permission::orderBy('name')->get();

        $this->canEdit = auth()->user()->can('matrix.edit');

        $this->buildMatrix();
    }

    public function buildMatrix()
    {
        $this->groupedPermissions = [];

        foreach ($this->permissions as $permission) {

            // Detectar módulo (antes del primer punto)
            $parts = explode('.', $permission->name);
            $module = count($parts) > 1 ? $parts[0] : 'general';

            // Inicializar grupo
            if (!isset($this->groupedPermissions[$module])) {
                $this->groupedPermissions[$module] = [];
            }

            // Agregar permiso al grupo
            $this->groupedPermissions[$module][] = $permission;

            // Construir matriz
            foreach ($this->roles as $role) {
                $this->matrix[$permission->name][$role->name] =
                    $role->hasPermissionTo($permission->name);
            }
        }

        // Ordenar módulos alfabéticamente
        ksort($this->groupedPermissions);
    }

    public function togglePermission($permissionName, $roleName)
    {
        if (!$this->canEdit) {
            return;
        }

        $role = Role::where('name', $roleName)->first();
        $permission = Permission::where('name', $permissionName)->first();

        if (!$role || !$permission) return;

        if ($role->hasPermissionTo($permissionName)) {
            $role->revokePermissionTo($permissionName);
            $this->matrix[$permissionName][$roleName] = false;
        } else {
            $role->givePermissionTo($permissionName);
            $this->matrix[$permissionName][$roleName] = true;
        }

        $this->dispatch('toast', type: 'success', message: 'Permiso actualizado');
    }

    public function toggleModule($module, $roleName, $value)
    {
        if (!$this->canEdit) return;

        $role = Role::where('name', $roleName)->first();
        if (!$role) return;

        $permissions = $this->groupedPermissions[$module] ?? [];

        foreach ($permissions as $permission) {
            if ($value) {
                $role->givePermissionTo($permission->name);
                $this->matrix[$permission->name][$roleName] = true;
            } else {
                $role->revokePermissionTo($permission->name);
                $this->matrix[$permission->name][$roleName] = false;
            }
        }

        $this->dispatch('toast', type: 'success', message: 'Módulo actualizado');
    }

    public function getModuleState($module, $roleName)
    {
        $permissions = $this->groupedPermissions[$module] ?? [];

        $total = count($permissions);
        $checked = 0;

        foreach ($permissions as $permission) {
            if ($this->matrix[$permission->name][$roleName] ?? false) {
                $checked++;
            }
        }

        if ($checked === 0) return 'none';
        if ($checked === $total) return 'all';

        return 'partial';
    }

    public function render()
    {
        return view('livewire.sys.permissions-matrix');
    }
}