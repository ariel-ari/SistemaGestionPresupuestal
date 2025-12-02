<?php

use Livewire\Volt\Component;
use Illuminate\View\View;
use App\Models\User;
use Spatie\Permission\Models\Role;

new class extends Component {

    public $roles;

    public function mount()
    {
        $this->roles = Role::get(['id', 'name']);
    }

    public function rendering(View $view): void
    {
        $view->title('Usuarios');
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:heading size="xl">Gestion de usuarios</flux:heading>
        <flux:text>
            Controla quién puede acceder al sistema y que puede hacer.
        </flux:text>
    </div>
    <livewire:users.create :roles="$roles"/>
    <livewire:users.edit :roles="$roles"/>
    <div>
        <livewire:components.table 
            title="Usuarios del sistema"
            description="Crea nuevos usuarios, asignales un rol y habilitalos o deshabilitalos cuando sea necesario."
            :columns="[
                'name' => ['label' => 'Usuario', 'width' => 'w-30'],
                'email' => ['label' => 'Correo', 'width' => 'w-60'],
                'role_name' => ['label' => 'Rol', 'width' => 'w-30'],
                'status' => ['label' => 'Estado', 'width' => 'w-60'],
            ]" 
            :searchableColumns="['name', 'email', 'roles.name']"
            :eagerLoad="['roles']"
            modelClass="App\Models\User"
            tableId="users"
            :perPage="20"
            :showButton="true"
            :buttonName="'Crear usuario'"
            :buttonIcon="'user-plus'"
            :showActions="true"
            :canEnableDisable="true"
            :canEdit="true"
            :isSearchable="true" />
    </div>
</div>
