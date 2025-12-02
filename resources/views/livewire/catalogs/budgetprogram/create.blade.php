<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\BudgetProgramForm;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public BudgetProgramForm $form;

    #[On('reset-form-budget_program')]
    public function resetFormBudgetProgram()
    {
        $this->resetForm();
    }

    public function save()
    {
        try {
            $this->form->store();
            Flux::modal('create-budget_programs')->close();
            $this->dispatch('refresh-table.budget_programs');
            $this->resetForm();
            LivewireAlert::title('Programa presupuestal agregado')->text('El programa presupuestal se agrego correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }
}; ?>

<flux:modal @close="resetFormBudgetProgram" name="create-budget_programs" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Agregar programa presupuestal</flux:heading>
    <flux:text>Complete el codigo y el nombre del programa presupuestal.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.code" label="Codigo" type="text" required placeholder="Codigo" />
        <flux:input wire:model="form.name" label="Programa presupuestal" type="text" required
            placeholder="Programa presupuestal" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Agregar</flux:button>
        </div>
    </form>
</flux:modal>
