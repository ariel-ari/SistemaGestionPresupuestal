<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\PurposeForm;
use Livewire\Attributes\On;
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

    public function save()
    {
        try {
            $this->form->store();
            Flux::modal('create-purposes')->close();
            $this->dispatch('refresh-table.purposes');
            $this->resetForm();
            LivewireAlert::title('Finalidad Pública agregada')->text('La finalidad pública se agrego correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }
}; ?>

<flux:modal @close="resetFormPurpose" name="create-purposes" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Agregar finalidad pública</flux:heading>
    <flux:text>Complete el nombre de la finalidad pública.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.name" label="Finalidad Pública" type="text" required
            placeholder="Finalidad Pública" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Agregar</flux:button>
        </div>
    </form>
</flux:modal>
