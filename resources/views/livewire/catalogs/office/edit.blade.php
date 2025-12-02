<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\OfficeForm;
use App\Models\Office;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public OfficeForm $form;

    #[On('edit-item.offices')]
    public function editItem($id)
    {
        try {
            $this->form->setModel(Office::findOrFail($id));
            Flux::modal('edit-office')->show();
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    public function save()
    {
        try {
            $this->form->update();
            Flux::modal('edit-office')->close();
            $this->dispatch('refresh-table.offices');
            LivewireAlert::title('Centro de costo actualizado')->text('El centro de costo se actualizo correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }

    public function resetFormOffice()
    {
        $this->resetForm();
    }
}; ?>


<flux:modal @close="resetFormOffice" name="edit-office" class="grid gap-2">
    <flux:heading class="mt-2" size="lg">Editar centro de costo</flux:heading>
    <flux:text>Edita los campos que sean necesarios.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.code" label="Codigo" type="text" required placeholder="Codigo" />
        <flux:input wire:model="form.name" label="Centro de costo" type="text" required
            placeholder="Centro de costo" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Guardar Cambios</flux:button>
        </div>
    </form>
</flux:modal>
