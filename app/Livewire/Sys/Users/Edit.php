<?php

namespace App\Livewire\Sys\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    public User $user;

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public bool $is_active = true;

    /**
     * Roles disponibles para asignación.
     *
     * Se cargan desde la base para mantener el formulario alineado
     * con la configuración real del sistema.
     */
    public array $availableRoles = [];

    /**
     * Roles actualmente seleccionados en el formulario.
     *
     * Se sincronizan con el usuario únicamente si el usuario autenticado
     * cuenta con permiso para asignar roles.
     */
    public array $selectedRoles = [];

    /**
     * Carga el estado inicial del formulario a partir del usuario recibido.
     */
    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email ?? '';
        $this->is_active = (bool) $user->is_active;

        $this->availableRoles = Role::query()
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $this->selectedRoles = $user->roles()
            ->pluck('name')
            ->toArray();
    }

    /**
     * Reglas base para actualización de usuario.
     *
     * Se ignora el propio registro al validar unicidad.
     * selectedRoles solo se valida como arreglo; la asignación real
     * está condicionada por permiso.
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($this->user->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'is_active' => ['boolean'],
            'selectedRoles' => ['array'],
            'selectedRoles.*' => ['string', Rule::in($this->availableRoles)],
        ];
    }

    /**
     * Actualiza la información básica del usuario y, cuando corresponde,
     * sincroniza sus roles.
     */
    public function save()
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'is_active' => $this->is_active,
        ]);

        // La asignación de roles se resuelve solo por roles,
        // evitando permisos directos sobre usuarios.
        if (Auth::user()?->can('sys.users.assign-roles')) {
            $this->user->syncRoles($this->selectedRoles);
        }


        session()->flash('success', 'Usuario actualizado correctamente.');

        return redirect()->route('sys.users.index');
    }
    public function render()
    {
        return view('livewire.sys.users.edit');
    }
}