<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\ClassifierForm;
use App\Models\Classifier;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public ClassifierForm $form;

    #[On('edit-item.classifiers')]
    public function editItem($id)
    {
        try {
            $this->form->setModel(Classifier::findOrFail($id));
            Flux::modal('edit-classifier')->show();
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    public function save()
    {
        try {
            $this->form->update();
            Flux::modal('edit-classifier')->close();
            $this->dispatch('refresh-table.classifiers');
            LivewireAlert::title('Clasificador de gastos actualizado')->text('El clasificador de gastos se actualizo correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }

    public function resetFormClassifier()
    {
        $this->resetForm();
    }
}; ?>


<flux:modal @close="resetFormClassifier" name="edit-classifier" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Editar clasificador de gastos</flux:heading>
    <flux:text>Edita los campos que sean necesarios.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.code" label="Código" type="text" required placeholder="Código" />
        <flux:input wire:model="form.name" label="Clasificador de gastos" type="text" required
            placeholder="Clasificador de gastos" />
        <flux:input wire:model="form.alternate_name" label="Nombre alternativo" type="text" placeholder="Nombre alternativo" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Guardar Cambios</flux:button>
        </div>
    </form>
</flux:modal>
