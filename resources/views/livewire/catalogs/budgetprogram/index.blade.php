<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\BudgetProgram;
use Illuminate\View\View;

new class extends Component {
    use WithPagination;

    public $nameFilter = '';

    public function rendering(View $view): void
    {
        $view->title('Programas presupuestales');
    }

    public function resetForm()
    {
        $this->dispatch('reset-form-budget_program');
    }

    #[On('close-budget_program-modal')]
    public function closeModal()
    {
        $this->modal('create-budget_program')->close();
    }

    #[On('delete-item.budget_programs')]
    public function deleteItem($id)
    {
        try {
            $budget_program = BudgetProgram::findOrFail($id['id']);
            $budget_program->delete();
            LivewireAlert::title('Programa presupuestal eliminado correctamente')->toast()->success()->position('top-end')->show();
            $this->dispatch('refresh-table.budget_programs');
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:heading size="xl">
            Programas presupuestales
        </flux:heading>
        <flux:text>Administra los programas presupuestales.</flux:text>
    </div>
    <!-- Modal de creacion -->
    <livewire:catalogs.budgetprogram.create />
    <!-- Modal de edicion -->
    <livewire:catalogs.budgetprogram.edit />
    <!-- Tabla -->
    <livewire:components.table title="Programas presupuestales" description="Crea nuevos programas presupuestales y habilita o deshabilita según sea necesario." :columns="[
        'code' => ['label' => 'Código', 'width' => 'w-24'],
        'name' => ['label' => 'Programa presupuestal', 'width' => 'w-64'],
        'status' => ['label' => 'Estado', 'width' => 'w-24'],
    ]" :searchableColumns="['code', 'name']" modelClass="App\Models\BudgetProgram"
        tableId="budget_programs" :perPage="20" :showActions="true" :canEdit="true" :canEnableDisable="true" :showButton="true" buttonName="Nuevo programa presupuestal" />
</div>
