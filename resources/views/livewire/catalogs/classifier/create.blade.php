<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\ClassifierForm;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public ClassifierForm $form;

    #[On('reset-form-classifier')]
    public function resetFormClassifier()
    {
        $this->resetForm();
    }

    public function save()
    {
        try {
            $this->form->store();
            Flux::modal('create-classifiers')->close();
            $this->dispatch('refresh-table.classifiers');
            $this->resetForm();
            LivewireAlert::title('Clasificador de gastos agregado')->text('El clasificador de gastos se agrego correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }
}; ?>

<flux:modal @close="resetFormClassifier" name="create-classifiers" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Agregar clasificador de gastos</flux:heading>
    <flux:text>Complete el codigo y el nombre del clasificador de gastos.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.code" label="Código" type="text" required placeholder="Código" />
        <flux:input wire:model="form.name" label="Clasificador de gastos" type="text" required
            placeholder="Clasificador de gastos" />
        <flux:input wire:model="form.alternate_name" label="Nombre alternativo" type="text" placeholder="Nombre alternativo" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Agregar</flux:button>
        </div>
    </form>
</flux:modal>
