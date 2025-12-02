<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\UserForm;
use App\Models\User;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {
    use ResetsForm;

    public UserForm $form;
    public $roles;

    #[On('edit-item.users')]
    public function editItem($id)
    {
        try {
            $this->form->setModel(User::findOrFail($id));
            Flux::modal('edit-user')->show();
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    public function save()
    {
        try {
            $this->form->update();
            Flux::modal('edit-user')->close();
            $this->dispatch('refresh-table.users');
            LivewireAlert::title('Usuario actualizado')->text('El usuario se actualizo correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }

    public function resetFormUser()
    {
        $this->resetForm();
    }
}; ?>


<flux:modal wire:key="modal-edit-user" @close="resetFormUser" name="edit-user" class="grid gap-2 md:w-96" x-data x-cloak>
    <flux:heading class="mt-2" size="lg">Editar usuario</flux:heading>
    <flux:text>Edita los campos que sean necesarios.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.name" label="Nombre" type="text" required placeholder="Nombre" />
        <flux:input wire:model="form.email" label="Correo" type="email" required placeholder="Correo" />
        <flux:select wire:model="form.role_id" label="Rol" required placeholder="Selecciona un rol">
            @foreach ($roles as $role)
                <flux:select.option value="{{ $role->id }}">{{ $role->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Guardar Cambios</flux:button>
        </div>
    </form>
</flux:modal>
