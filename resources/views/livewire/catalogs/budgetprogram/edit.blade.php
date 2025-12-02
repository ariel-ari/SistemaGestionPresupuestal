<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\BudgetProgramForm;
use App\Models\BudgetProgram;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public BudgetProgramForm $form;

    #[On('edit-item.budget_programs')]
    public function editItem($id)
    {
        try {
            $this->form->setModel(BudgetProgram::findOrFail($id));
            Flux::modal('edit-budget_program')->show();
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    public function save()
    {
        try {
            $this->form->update();
            Flux::modal('edit-budget_program')->close();
            $this->dispatch('refresh-table.budget_programs');
            LivewireAlert::title('Programa presupuestal actualizado')->text('El programa presupuestal se actualizo correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }

    public function resetFormBudgetProgram()
    {
        $this->resetForm();
    }
}; ?>


<flux:modal @close="resetFormBudgetProgram" name="edit-budget_program" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Editar programa presupuestal</flux:heading>
    <flux:text>Edita los campos que sean necesarios.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.code" label="Codigo" type="text" required placeholder="Codigo" />
        <flux:input wire:model="form.name" label="Programa presupuestal" type="text" required
            placeholder="Programa presupuestal" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Guardar Cambios</flux:button>
        </div>
    </form>
</flux:modal>
