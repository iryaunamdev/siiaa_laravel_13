<?php

namespace App\Livewire\Sys\RolesPermissions;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Index extends Component
{
    public $roles = [];
    public $permissions = [];
    public array $groupedPermissions = [];

    public bool $roleModal = false;
    public bool $permissionModal = false;
    public bool $confirmDeleteModal = false;

    public ?int $editingRoleId = null;
    public ?int $editingPermissionId = null;

    public string $roleName = '';
    public array $selectedPermissions = [];

    public string $newPermissions = '';
    public string $permissionName = '';

    public ?string $deleteType = null;
    public ?int $deleteId = null;
    public string $deleteName = '';

    public function mount(): void
    {
        $this->authorizeSuperAdmin();
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.sys.roles-permissions.index');
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
    }

    public function loadData(): void
    {
        $this->roles = Role::query()
            ->withCount('users')
            ->with('permissions:id,name')
            ->orderBy('name')
            ->get();

        $this->permissions = Permission::query()
            ->orderBy('name')
            ->get();

        $this->groupPermissions();
    }

    private function groupPermissions(): void
    {
        $this->groupedPermissions = $this->permissions
            ->sortBy('name')
            ->groupBy(fn($permission) => str($permission->name)->before('.')->toString())
            ->toArray();
    }

    public function openCreateRoleModal(): void
    {
        $this->resetRoleForm();
        $this->roleModal = true;
    }

    public function openEditRoleModal(int $roleId): void
    {
        $this->authorizeSuperAdmin();

        $role = Role::with('permissions:id,name')->findOrFail($roleId);

        $this->editingRoleId = $role->id;
        $this->roleName = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->roleModal = true;
    }

    public function saveRole(): void
    {
        $this->authorizeSuperAdmin();

        $validated = $this->validate([
            'roleName' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_-]+$/'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::updateOrCreate(
            ['id' => $this->editingRoleId],
            ['name' => $validated['roleName']]
        );

        $role->syncPermissions($validated['selectedPermissions'] ?? []);

        $this->loadData();
        $this->resetRoleForm();

        $this->dispatch(
            'toast',
            type: 'success',
            message: $this->editingRoleId ? 'Rol actualizado correctamente.' : 'Rol creado correctamente.'
        );
    }

    public function closeRoleModal(): void
    {
        $this->resetRoleForm();
    }

    private function resetRoleForm(): void
    {
        $this->reset([
            'roleModal',
            'editingRoleId',
            'roleName',
            'selectedPermissions',
        ]);

        $this->resetValidation();
    }

    public function openCreatePermissionModal(): void
    {
        $this->resetPermissionForm();
        $this->permissionModal = true;
    }

    public function openEditPermissionModal(int $permissionId): void
    {
        $this->authorizeSuperAdmin();

        $permission = Permission::findOrFail($permissionId);

        $this->editingPermissionId = $permission->id;
        $this->permissionName = $permission->name;
        $this->permissionModal = true;
    }

    public function savePermission(): void
    {
        $this->authorizeSuperAdmin();

        if ($this->editingPermissionId) {
            $validated = $this->validate([
                'permissionName' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-z0-9_-]+\.[a-z0-9_-]+$/',
                    'unique:permissions,name,' . $this->editingPermissionId,
                ],
            ]);

            $permission = Permission::findOrFail($this->editingPermissionId);
            $permission->update([
                'name' => $validated['permissionName'],
            ]);

            $this->loadData();
            $this->resetPermissionForm();

            $this->dispatch('toast', type: 'success', message: 'Permiso actualizado correctamente.');
            return;
        }

        $this->createPermissions();
        $this->permissionModal = false;
    }

    public function createPermissions(): void
    {
        $this->authorizeSuperAdmin();

        if (blank($this->newPermissions)) {
            $this->dispatch('toast', type: 'warning', message: 'Ingresa al menos un permiso para crear.');
            return;
        }

        $items = collect(preg_split('/[\n,]+/', $this->newPermissions))
            ->map(fn($item) => trim($item))
            ->filter()
            ->unique()
            ->values();

        $created = [];
        $duplicated = [];
        $invalid = [];

        foreach ($items as $name) {
            if (! preg_match('/^[a-z0-9_-]+\.[a-z0-9_-]+$/', $name)) {
                $invalid[] = $name;
                continue;
            }

            if (Permission::where('name', $name)->exists()) {
                $duplicated[] = $name;
                continue;
            }

            Permission::create(['name' => $name]);
            $created[] = $name;
        }

        $this->newPermissions = '';
        $this->loadData();

        if ($created) {
            $this->dispatch('toast', type: 'success', message: count($created) . ' permiso(s) creado(s).');
        }

        if ($duplicated) {
            $this->dispatch('toast', type: 'info', message: count($duplicated) . ' permiso(s) ya existían.');
        }

        if ($invalid) {
            $this->dispatch('toast', type: 'warning', message: count($invalid) . ' permiso(s) inválido(s).');
        }
    }

    public function closePermissionModal(): void
    {
        $this->resetPermissionForm();
    }

    private function resetPermissionForm(): void
    {
        $this->reset([
            'permissionModal',
            'editingPermissionId',
            'permissionName',
            'newPermissions',
        ]);

        $this->resetValidation();
    }

    public function confirmDelete(string $type, int $id): void
    {
        $this->authorizeSuperAdmin();

        if ($type === 'role') {
            $item = Role::withCount(['users', 'permissions'])->findOrFail($id);

            if ($item->users_count > 0 || $item->permissions_count > 0) {
                $this->dispatch(
                    'toast',
                    type: 'warning',
                    message: 'No se puede eliminar un rol con usuarios o permisos asignados.'
                );

                return;
            }
        } else {
            $item = Permission::findOrFail($id);
        }

        $this->deleteType = $type;
        $this->deleteId = $id;
        $this->deleteName = $item->name;
        $this->confirmDeleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        $this->authorizeSuperAdmin();

        if ($this->deleteType === 'role') {
            $role = Role::withCount(['users', 'permissions'])->findOrFail($this->deleteId);

            if ($role->users_count > 0 || $role->permissions_count > 0) {
                $this->dispatch(
                    'toast',
                    type: 'warning',
                    message: 'Solo se puede eliminar un rol sin usuarios ni permisos asignados.'
                );

                $this->resetDeleteForm();
                return;
            }

            $role->delete();

            $message = 'Rol eliminado correctamente.';
        } else {
            $permission = Permission::findOrFail($this->deleteId);

            // Solo elimina la relación de ESTE permiso con los roles que lo tengan.
            // No elimina roles.
            $permission->roles()->detach();

            $permission->delete();

            $message = 'Permiso eliminado correctamente. También fue retirado de los roles vinculados.';
        }

        $this->loadData();
        $this->resetDeleteForm();

        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function closeDeleteModal(): void
    {
        $this->resetDeleteForm();
    }

    private function resetDeleteForm(): void
    {
        $this->reset([
            'confirmDeleteModal',
            'deleteType',
            'deleteId',
            'deleteName',
        ]);
    }
}