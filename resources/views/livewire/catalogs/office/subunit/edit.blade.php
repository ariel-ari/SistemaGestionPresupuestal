<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\SubunitForm;
use App\Models\Subunit;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public SubunitForm $form;

    #[On('edit-item.subunits')]
    public function editItem($id)
    {
        try {
            $this->form->setModel(Subunit::findOrFail($id));
            Flux::modal('edit-subunit')->show();
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    public function save()
    {
        try {
            $this->form->update();
            Flux::modal('edit-subunit')->close();
            $this->dispatch('refresh-table.subunits');
            LivewireAlert::title('Finalidad actualizada')->text('La finalidad se actualizo correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }

    public function resetFormSubunit()
    {
        $this->resetForm();
    }
}; ?>


<flux:modal @close="resetFormSubunit" name="edit-subunit" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Editar finalidad</flux:heading>
    <flux:text>Edita los campos que sean necesarios.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.name" label="Finalidad" type="text" required
            placeholder="Finalidad" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Guardar Cambios</flux:button>
        </div>
    </form>
</flux:modal>
