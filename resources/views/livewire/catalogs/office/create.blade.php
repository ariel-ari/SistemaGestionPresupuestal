<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\OfficeForm;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public OfficeForm $form;

    #[On('reset-form-office')]
    public function resetFormOffice()
    {
        $this->resetForm();
    }

    public function save()
    {
        try {
            $this->form->store();
            Flux::modal('create-offices')->close();
            $this->dispatch('refresh-table.offices');
            $this->resetForm();
            LivewireAlert::title('Centro de costo agregado')->text('El centro de costo se agrego correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }
}; ?>

<flux:modal @close="resetFormOffice" name="create-offices" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Nuevo centro de costo</flux:heading>
    <flux:text>Ingresa el codigo y el nombre del centro de costo.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.code" label="Codigo" type="text" required placeholder="Codigo" />
        <flux:input wire:model="form.name" label="Centro de costo" type="text" required
            placeholder="Centro de costo" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Agregar</flux:button>
        </div>
    </form>
</flux:modal>
