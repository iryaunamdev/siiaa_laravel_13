<?php

namespace App\Livewire\Sys\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Index extends Component
{
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

        abort_unless(
            auth()->user()->can($isEditing ? 'users.update' : 'users.create'),
            403
        );

        if (! empty($this->selectedRoles)) {
            abort_unless(auth()->user()->can('users.assign_roles'), 403);
        }

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

        $user = $isEditing
            ? User::findOrFail($this->editingUserId)
            : new User();

        $user->username = $validated['username'];
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! $isEditing || $this->changePassword) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (auth()->user()->can('users.assign_roles')) {
            $user->syncRoles($this->selectedRoles);
        }

        $this->userModal = false;
        $this->resetUserForm();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $isEditing
                ? 'Usuario actualizado correctamente.'
                : 'Usuario creado correctamente.',
        ]);
    }

    public function openCreateModal()
    {
        abort_unless(auth()->user()->can('users.create'), 403);

        $this->resetUserForm();

        $this->editingUserId = null;
        $this->isLocalUser = true;
        $this->changePassword = true;
        $this->userModal = true;
    }

    public function openEditModal(int $userId)
    {
        abort_unless(auth()->user()->can('users.update'), 403);

        $user = User::findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->username = $user->username;
        $this->name = $user->name;
        $this->email = $user->email;

        $this->selectedRoles = $user->roles()
            ->pluck('name')
            ->toArray();

        $this->changePassword = false;
        $this->password = null;
        $this->password_confirmation = null;

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
        ]);

        $this->isLocalUser = true;
    }

    public function generatePassword()
    {
        abort_unless(auth()->user()->can('users.change_password'), 403);

        $password = Str::random(12);

        $this->password = $password;
        $this->password_confirmation = $password;
    }

    public function render()
    {
        return view('livewire.sys.users.index', [
            'users' => $this->users,
        ]);
    }
}