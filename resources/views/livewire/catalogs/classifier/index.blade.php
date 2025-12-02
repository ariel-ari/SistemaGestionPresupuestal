<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Classifier;
use Illuminate\View\View;
use Livewire\Attributes\Url;

new class extends Component {
    use WithPagination;

    public $nameFilter = '';

    #[Url(as: 'classifier', except: null)]
    public $selectedClassifierId = null;
    public $selectedClassifierName = null;

    public function mount()
    {
        if (!$this->selectedClassifierId) {
            $firstClassifier = Classifier::first();
            if ($firstClassifier) {
                $this->selectedClassifierId = $firstClassifier->id;
                $this->selectedClassifierName = $firstClassifier->name;
            }
        }
    }

    public function rendering(View $view): void
    {
        $view->title('Clasificadores de gastos');
    }

    public function resetForm()
    {
        $this->dispatch('reset-form-classifier');
    }

    #[On('close-classifier-modal')]
    public function closeModal()
    {
        $this->modal('create-classifier')->close();
    }

    #[On('delete-item.classifiers')]
    public function deleteItem($id)
    {
        try {
            $classifier = Classifier::findOrFail($id['id']);
            $classifier->delete();
            LivewireAlert::title('Clasificador de gastos eliminado correctamente')->toast()->success()->position('top-end')->show();
            $this->dispatch('refresh-table.classifiers');
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    #[On('classifiers-row-selected')]
    public function handleClassifierSelection($itemId, $item)
    {
        $this->selectedClassifierId = $itemId;
        $this->selectedClassifierName = $item['name'];
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:heading size="xl">Gestión de clasificadores y subclasificadores de gastos</flux:heading>
        <flux:text>Administra los clasificadores y subclasificadores de gastos.</flux:text>
    </div>
    <!-- Modal de creacion -->
    <livewire:catalogs.classifier.create />
    <livewire:catalogs.classifier.subclassifier.create :classifierId="$selectedClassifierId" />
    <!-- Modal de edicion -->
    <livewire:catalogs.classifier.edit />
    <livewire:catalogs.classifier.subclassifier.edit />
    <!-- Tabla de clasificadores de gastos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <livewire:components.table 
        wire:key="classifiers-table-{{ $selectedClassifierId }}" title="Clasificadores de gastos"
        description="Crea nuevos clasificadores y subclasificadores de gastos, habilita y deshabilita clasificadores y subclasificadores de gastos cuando se necesario."
        :columns="[
                'code' => ['label' => 'Código', 'width' => 'w-24'],
                'name' => ['label' => 'Clasificador de gastos', 'width' => 'w-64'],
                'alternate_name' => ['label' => 'Nombre alternativo', 'width' => 'w-24'],
                'status' => ['label' => 'Estado', 'width' => 'w-24'],
        ]" 
        :searchableColumns="['code', 'name', 'alternate_name']" 
        modelClass="App\Models\Classifier" 
        tableId="classifiers" 
        :perPage="20"
        :showActions="true" 
        :canEdit="true" 
        :canEnableDisable="true" 
        :showButton="true" 
        :buttonName="'Nuevo clasificador de gastos'"
        :selectedRowId="$selectedClassifierId"
        :isSelectable="true" />

        <livewire:components.table 
        wire:key="subclassifiers-table-{{$selectedClassifierId}}" 
        title="Subclasificadores de gastos" 
        subtitle="{{ $selectedClassifierName }}"
        description="Crea nuevos subclasificadores de gastos, habilita y deshabilita subclasificadores de gastos cuando se necesario."
        :columns="[
                'code' => ['label' => 'Código', 'width' => 'w-24'],
                'name' => ['label' => 'Subclasificador de gastos', 'width' => 'w-64'],
                'status' => ['label' => 'Estado', 'width' => 'w-24'],
            ]" 
        :searchableColumns="['code', 'name']" 
        modelClass="App\Models\Subclassifier" 
        tableId="subclassifiers"
        :perPage="20" 
        :showActions="true" 
        :canEdit="true" 
        :canEnableDisable="true" 
        :showButton="true" 
        :buttonName="'Nuevo subclasificador de gastos'" 
        :filters="['classifier_id' => $selectedClassifierId]"
        :isSearchable="true"/>
    </div>
