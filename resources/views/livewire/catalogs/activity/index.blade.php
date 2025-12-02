<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Activity;
use Illuminate\View\View;

new class extends Component {
    use WithPagination;

    public $nameFilter = '';

    public function rendering(View $view): void
    {
        $view->title('Actividades');
    }
    
    #[On('close-activity-modal')]
    public function closeModal()
    {
        $this->modal('create-activity')->close();
    }

    #[On('delete-item.activities')]
    public function deleteItem($id)
    {
        try {
            $activity = Activity::findOrFail($id['id']);
            $activity->delete();
            LivewireAlert::title('Actividad eliminada correctamente')->toast()->success()->position('top-end')->show();
            $this->dispatch('refresh-table.activities');
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:heading size="xl">
            Gestión de actividades
        </flux:heading>
        <flux:text>Administra las actividades.</flux:text>
    </div>
    <!-- Modal de creacion -->
    <livewire:catalogs.activity.create />
    <!-- Modal de edicion -->
    <livewire:catalogs.activity.edit />
    <!-- Tabla de actividades -->
    <livewire:components.table 
    title="Actividades" 
    description="Crea nuevas actividades y habilita o deshabilita cuando sea necesario." 
    :columns="[
        'name' => ['label' => 'Actividad', 'width' => 'w-64'],
        'status' => ['label' => 'Estado', 'width' => 'w-64'],
    ]" 
    :searchableColumns="['name']" 
    modelClass="App\Models\Activity" 
    tableId="activities"
    :perPage="20" 
    :showActions="true" 
    :canEdit="true" 
    :canEnableDisable="true" 
    :showButton="true" 
    :buttonName="'Nueva actividad'"
    :isSearchable="true"/>
</div>
