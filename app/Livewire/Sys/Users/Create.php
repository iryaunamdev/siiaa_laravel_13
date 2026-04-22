<?php

namespace App\Livewire\Sys\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $auth_type = 'local';
    public bool $is_active = true;

    /**
     * Reglas de validación base para creación de usuario.
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'auth_type' => ['required', Rule::in(['local'])],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Crea un nuevo usuario en el sistema.
     *
     * Este método representa el primer flujo real de escritura
     * dentro del módulo de usuarios.
     */
    public function save()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'password' => Hash::make($this->password),
            'auth_type' => $this->auth_type,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Usuario creado correctamente.');

        return redirect()->route('sys.users.index');
    }

    public function render()
    {
        return view('livewire.sys.users.create');
    }
}