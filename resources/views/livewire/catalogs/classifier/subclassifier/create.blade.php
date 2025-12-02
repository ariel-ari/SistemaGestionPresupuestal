<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\SubclassifierForm;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;
    public $classifierId;

    public SubclassifierForm $form;

    #[On('classifiers-row-selected')]
    public function updateClassifierId($itemId, $item)
    {
        $this->classifierId = $itemId;
    }

    #[On('reset-form-subclassifier')]
    public function resetFormSubclassifier()
    {
        $this->resetForm();
    }

    public function save()
    {
        $this->form->classifier_id = $this->classifierId;
        try {
            $this->form->store();
            Flux::modal('create-subclassifiers')->close();
            $this->dispatch('refresh-table.subclassifiers');
            $this->resetForm();
            LivewireAlert::title('Subclasificador agregado')->text('El subclasificador se agrego correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }

}; ?>

<flux:modal @close="resetFormSubclassifier" name="create-subclassifiers"  class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Nuevo Subclasificador</flux:heading>
    <flux:text>Ingresa el nombre del subclasificador.</flux:text>

    {{-- Debug: Mostrar la clasificador seleccionada --}}
    @if (!$classifierId)
        <flux:badge color="red" size="sm">⚠️ No hay clasificador seleccionado</flux:badge>
    @endif

    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.code" label="Codigo" type="text" required placeholder="Codigo" />
        <flux:input wire:model="form.name" label="Subclasificador" type="text" required placeholder="Subclasificador" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Agregar</flux:button>
        </div>
    </form>
</flux:modal>
