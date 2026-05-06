<?php

namespace App\Livewire\Sys\Users;

use App\Livewire\Concerns\AuthorizesSIIAA;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use AuthorizesSIIAA;
    public bool $userModal = false;
    public ?int $editingUserId = null;

    public string $username = '';
    public string $name = '';
    public string $email = '';

    public array $selectedRoles = [];

    public bool $isLocalUser = true;
    public bool $changePassword = false;

    public ?string $password = null;
    public ?string $password_confirmation = null;

    public $roles = [];

    public bool $confirmDeleteModal = false;
    public ?int $deleteId = null;
    public ?string $deleteName = null;
    public bool $confirmResetTwoFactorModal = false;
    public ?int $resetTwoFactorUserId = null;
    public ?string $resetTwoFactorUserName = null;
    public bool $editingUserHasTwoFactor = false;

    public string $search = '';
    public string $status = 'all';
    public string $authType = 'all';
    public string $roleFilter = 'all';

    public bool $isLdapUser = false;
    public string $auth_type = 'local';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
        'authType' => ['except' => 'all'],
        'roleFilter' => ['except' => 'all'],
    ];

    public function mount()
    {
        $this->roles = Role::query()
            ->orderBy('name')
            ->get();
    }
    /**
     * Obtiene el listado base de usuarios.
     *
     * En esta primera etapa se cargan solo los campos esenciales
     * para validar el módulo, la autorización y el consumo real
     * de datos desde la base.
     */
    public function getUsersProperty()
    {
        return User::query()
            ->select([
                'id',
                'name',
                'username',
                'email',
                'auth_type',
                'is_active',
                'last_login_at',
            ])
            ->orderBy('name')
            ->get();
    }

    public function saveUser(): void
    {
        $isEditing = filled($this->editingUserId);

        $this->authorizePermission(
            $isEditing ? 'users.update' : 'users.create'
        );

        if (! empty($this->selectedRoles)) {
            abort_unless(auth()->user()->can('users.assign_roles'), 403);
        }

        $user = $isEditing
            ? User::findOrFail($this->editingUserId)
            : new User();

        /*
    |--------------------------------------------------------------------------
    | Usuario LDAP
    |--------------------------------------------------------------------------
    | Los usuarios LDAP no se editan manualmente.
    | Solo se permite actualizar la asignación de roles.
    */
        if ($isEditing && $user->auth_type === 'ldap') {
            abort_unless(auth()->user()->can('users.assign_roles'), 403);

            $this->validate([
                'selectedRoles' => ['array'],
                'selectedRoles.*' => [
                    'string',
                    Rule::exists('roles', 'name'),
                ],
            ]);

            $user->syncRoles($this->selectedRoles);

            $this->userModal = false;
            $this->resetUserForm();

            $this->dispatch(
                'toast',
                type: 'success',
                message: 'Roles del usuario LDAP actualizados correctamente.'
            );

            return;
        }

        /*
    |--------------------------------------------------------------------------
    | Usuario local
    |--------------------------------------------------------------------------
    */
        if ($this->changePassword) {
            abort_unless(auth()->user()->can('users.change_password'), 403);
        }

        $validated = $this->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($this->editingUserId),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingUserId),
            ],
            'selectedRoles' => [
                'array',
            ],
            'selectedRoles.*' => [
                'string',
                Rule::exists('roles', 'name'),
            ],
            'password' => [
                $isEditing && ! $this->changePassword ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user->username = $validated['username'];
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->auth_type = 'local';

        if (! $isEditing || $this->changePassword) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (auth()->user()->can('users.assign_roles')) {
            $user->syncRoles($this->selectedRoles);
        }

        $this->userModal = false;
        $this->resetUserForm();

        $this->dispatch(
            'toast',
            type: 'success',
            message: $isEditing
                ? 'Usuario actualizado correctamente.'
                : 'Usuario creado correctamente.'
        );
    }

    public function openCreateModal()
    {
        $this->authorizePermission('users.create');
        $this->resetUserForm();

        $this->isLdapUser = false;
        $this->auth_type = 'local';

        $this->editingUserId = null;
        $this->isLocalUser = true;
        $this->changePassword = true;
        $this->userModal = true;
    }

    public function openEditModal(int $userId)
    {
        $this->authorizePermission('users.update');

        $user = User::findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->username = $user->username;
        $this->name = $user->name;
        $this->email = $user->email;

        $this->editingUserHasTwoFactor = filled($user->two_factor_secret)
            || filled($user->two_factor_confirmed_at);

        $this->selectedRoles = $user->roles()
            ->pluck('name')
            ->toArray();

        $this->changePassword = false;
        $this->password = null;
        $this->password_confirmation = null;

        $this->isLdapUser = $user->auth_type === 'ldap';
        $this->auth_type = $user->auth_type ?? 'local';
        $this->isLocalUser = $user->auth_type === 'local';

        $this->userModal = true;
    }

    private function resetUserForm(): void
    {
        $this->reset([
            'editingUserId',
            'username',
            'name',
            'email',
            'selectedRoles',
            'changePassword',
            'password',
            'password_confirmation',
            'auth_type',
            'isLdapUser',
            'editingUserHasTwoFactor',
        ]);

        $this->isLocalUser = true;
        $this->auth_type = 'local';
        $this->isLdapUser = false;
        $this->editingUserHasTwoFactor = false;
    }

    public function generatePassword()
    {
        $this->authorizePermission('users.change_password');

        $password = Str::random(12);

        $this->password = $password;
        $this->password_confirmation = $password;
    }

    public function confirmDelete(int $userId): void
    {
        $this->authorizePermission('users.delete');

        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            $this->dispatch('toast', type: 'error', message: 'No puedes eliminar tu propio usuario.');

            return;
        }

        $this->deleteId = $user->id;
        $this->deleteName = $user->name . ' (' . $user->username . ')';
        $this->confirmDeleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        $this->authorizePermission('users.delete');

        if (! $this->deleteId) {
            $this->resetDeleteForm();

            $this->dispatch('toast', type: 'error', message: 'No se encontró el usuario a eliminar.');

            return;
        }

        $user = User::findOrFail($this->deleteId);

        if ($user->id === auth()->id()) {
            $this->resetDeleteForm();

            $this->dispatch('toast', type: 'error', message: 'No puedes eliminar tu propio usuario.');

            return;
        }

        $user->syncRoles([]);
        $user->delete();

        $this->resetDeleteForm();

        $this->dispatch('toast', type: 'success', message: 'Usuario eliminado correctamente.');
    }

    public function confirmResetTwoFactor(int $userId): void
    {
        $this->authorizePermission('users.reset_2fa');

        $user = User::findOrFail($userId);

        if (! $user->two_factor_secret && ! $user->two_factor_confirmed_at) {
            $this->dispatch(
                'toast',
                type: 'info',
                message: 'Este usuario no tiene autenticación en dos factores configurada.'
            );

            return;
        }

        $this->resetTwoFactorUserId = $user->id;
        $this->resetTwoFactorUserName = $user->name . ' (' . $user->username . ')';
        $this->confirmResetTwoFactorModal = true;
    }

    public function resetTwoFactorConfirmed(): void
    {
        $this->authorizePermission('users.reset_2fa');

        if (! $this->resetTwoFactorUserId) {
            $this->resetTwoFactorForm();

            $this->dispatch(
                'toast',
                type: 'error',
                message: 'No se encontró el usuario para restablecer 2FA.'
            );

            return;
        }

        $user = User::findOrFail($this->resetTwoFactorUserId);

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->editingUserHasTwoFactor = false;

        $this->resetTwoFactorForm();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'La autenticación en dos factores fue restablecida correctamente.'
        );
    }

    public function resetTwoFactorForm(): void
    {
        $this->confirmResetTwoFactorModal = false;
        $this->resetTwoFactorUserId = null;
        $this->resetTwoFactorUserName = null;
    }

    public function resetDeleteForm(): void
    {
        $this->confirmDeleteModal = false;
        $this->deleteId = null;
        $this->deleteName = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status = 'all';
        $this->authType = 'all';
        $this->roleFilter = 'all';

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedAuthType(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }


    public function render()
    {
        $users = User::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('is_active', $this->status === 'active');
            })
            ->when($this->authType !== 'all', function ($query) {
                $query->where('auth_type', $this->authType);
            })
            ->when($this->roleFilter !== 'all', function ($query) {
                $query->role($this->roleFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.sys.users.index', [
            'users' => $users,
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }
}
