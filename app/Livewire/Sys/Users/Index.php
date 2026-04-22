<?php

namespace App\Livewire\Sys\Users;

use App\Models\User;
use Livewire\Component;

class Index extends Component
{
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

    public function render()
    {
        return view('livewire.sys.users.index', [
            'users' => $this->users,
        ]);
    }
}