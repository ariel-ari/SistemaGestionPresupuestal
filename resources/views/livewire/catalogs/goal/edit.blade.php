<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\GoalForm;
use App\Models\Goal;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public GoalForm $form;

    #[On('edit-item.goals')]
    public function editItem($id)
    {
        try {
            $this->form->setModel(Goal::findOrFail($id));
            Flux::modal('edit-goal')->show();
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    public function save()
    {
        try {
            $this->form->update();
            Flux::modal('edit-goal')->close();
            $this->dispatch('refresh-table.goals');
            LivewireAlert::title('Meta actualizada')->text('La meta se actualizo correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }

    public function resetFormGoal()
    {
        $this->resetForm();
    }
}; ?>


<flux:modal @close="resetFormGoal" name="edit-goal" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Editar meta</flux:heading>
    <flux:text>Edita los campos que sean necesarios.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.code" label="Codigo" type="text" required placeholder="Codigo" />
        <flux:input wire:model="form.name" label="Meta" type="text" required placeholder="Meta" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Guardar Cambios</flux:button>
        </div>
    </form>
</flux:modal>
