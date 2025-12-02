<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\ActivityForm;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public ActivityForm $form;

    #[On('reset-form-activity')]
    public function resetFormActivity()
    {
        $this->resetForm();
    }

    public function save()
    {
        try {
            $this->form->store();
            Flux::modal('create-activities')->close();
            $this->dispatch('refresh-table.activities');
            $this->resetForm();
            LivewireAlert::title('Actividad agregada')->text('La actividad se agrego correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }
}; ?>

<flux:modal @close="resetFormActivity" name="create-activities" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Agregar actividad</flux:heading>
    <flux:text>Complete el nombre de la actividad.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.name" label="Actividad" type="text" required
            placeholder="Actividad" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Agregar</flux:button>
        </div>
    </form>
</flux:modal>
