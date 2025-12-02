<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Goal;
use Illuminate\View\View;

new class extends Component {
    use WithPagination;

    public $nameFilter = '';

    public function rendering(View $view): void
    {
        $view->title('Metas');
    }

    public function resetForm()
    {
        $this->dispatch('reset-form-goal');
    }

    #[On('close-goal-modal')]
    public function closeModal()
    {
        $this->modal('create-goal')->close();
    }

    #[On('delete-item.goals')]
    public function deleteItem($id)
    {
        try {
            $goal = Goal::findOrFail($id['id']);
            $goal->delete();
            LivewireAlert::title('Meta eliminada correctamente')->toast()->success()->position('top-end')->show();
            $this->dispatch('refresh-table.goals');
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:heading size="xl">
            Metas
        </flux:heading>
        <flux:text>Administra las metas.</flux:text>
    </div>
    <!-- Modal de creacion -->
    <livewire:catalogs.goal.create />
    <!-- Modal de edicion -->
    <livewire:catalogs.goal.edit />
    <!-- Tabla de metas -->
    <livewire:components.table 
    title="Metas" 
    description="Crea Meta y habilita y deshabilita cuando sea necesario."
    :columns="[
            'code' => ['label' => 'Código', 'width' => 'w-24'],
            'name' => ['label' => 'Meta', 'width' => 'w-64'],
            'status' => ['label' => 'Estado', 'width' => 'w-24'],
        ]" 
    :searchableColumns="['code', 'name']" 
    modelClass="App\Models\Goal" 
    tableId="goals" 
    :perPage="20"
    :showActions="true" 
    :canEdit="true" 
    :canEnableDisable="true" 
    :showButton="true" 
    :buttonName="'Nueva meta'"
    :isSearchable="true"/>
</div>
