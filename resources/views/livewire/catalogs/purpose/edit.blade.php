<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\PurposeForm;
use App\Models\Purpose;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public PurposeForm $form;

    #[On('reset-form-purpose')]
    public function resetFormPurpose()
    {
        $this->resetForm();
    }

    #[On('edit-item.purposes')]
    public function editItem($id)
    {
        try {
            $this->form->setModel(Purpose::findOrFail($id));
            Flux::modal('edit-purpose')->show();
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    public function save()
    {
        try {
            $this->form->update();
            Flux::modal('edit-purpose')->close();
            $this->dispatch('refresh-table.purposes');
            LivewireAlert::title('Finalidad Pública actualizada')->text('La finalidad pública se actualizo correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }

}; ?>


<flux:modal @close="resetFormPurpose" name="edit-purpose" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Editar finalidad pública</flux:heading>
    <flux:text>Edita los campos que sean necesarios.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.name" label="Finalidad Pública" type="text" required
            placeholder="Finalidad Pública" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Guardar Cambios</flux:button>
        </div>
    </form>
</flux:modal>
