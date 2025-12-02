<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\SubunitForm;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {
    use ResetsForm;
    public $officeId;

    public SubunitForm $form;

    // Escuchar cuando se selecciona una oficina diferente
    #[On('offices-row-selected')]
    public function updateOfficeId($itemId, $item)
    {
        $this->officeId = $itemId;
    }

    #[On('reset-form-subunit')]
    public function resetFormSubunit()
    {
        $this->resetForm();
    }

    public function save()
    {
        $this->form->office_id = $this->officeId;
        try {
            $this->form->store();
            Flux::modal('create-subunits')->close();
            $this->dispatch('refresh-table.subunits');
            $this->resetForm();
            LivewireAlert::title('Finalidad agregada')->text('La finalidad se agrego correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }
}; ?>

<flux:modal @close="resetFormSubunit" name="create-subunits" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Nueva Finalidad</flux:heading>
    <flux:text>Ingresa el nombre de la finalidad.</flux:text>

    {{-- Debug: Mostrar la oficina seleccionada --}}
    @if (!$officeId)
        <flux:badge color="red" size="sm">⚠️ No hay oficina seleccionada</flux:badge>
    @endif

    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.name" label="Finalidad" type="text" required placeholder="Finalidad" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Agregar</flux:button>
        </div>
    </form>
</flux:modal>
