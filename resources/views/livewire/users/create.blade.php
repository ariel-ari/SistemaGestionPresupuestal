<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\UserForm;
use App\Traits\ResetsForm;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;

new class extends Component {
    use ResetsForm;

    public UserForm $form;
    public $roles;

    public function resetFormUser()
    {
        $this->resetForm();
    }

    public function save()
    {
        try {
            $this->form->store();
            Flux::modal('create-users')->close();
            $this->dispatch('refresh-table.users');
            $this->resetForm();
            LivewireAlert::title('Usuario agregado')->text('El usuario se agrego correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }
}; ?>

<flux:modal wire:key="modal-create-users" @close="resetFormUser" class="grid gap-2 md:w-96" name="create-users" x-data
    x-cloak>
    <flux:heading class="mt-2" size="lg">
        Crear usuario
    </flux:heading>
    <flux:text>
        Complete los datos básicos y asignale un rol.
    </flux:text>
    <form class="grid gap-2" wire:submit="save">
        <flux:input label="Nombre completo" type="text" wire:model="form.name" required
            placeholder="Ej: Ana Martinez" />
        <flux:input label="Correo electrónico" type="email" wire:model="form.email" required
            placeholder="Ej: ana.martinez@example.com" />
        <flux:select label="Rol" wire:model="form.role" required>
            <flux:select.option selected disabled value="null">Seleccione un rol</flux:select.option>
            @forelse ($roles as $role)
                <flux:select.option value="{{ $role->id }}">{{ $role->name }}</flux:select.option>
            @empty
                <flux:select.option value="">No hay roles</flux:select.option>
            @endforelse
        </flux:select>
        <flux:input label="Contraseña" type="password" wire:model="form.password" required placeholder="Contraseña"
            viewable />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Crear Usuario</flux:button>
        </div>
    </form>
</flux:modal>
