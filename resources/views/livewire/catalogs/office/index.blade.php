<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Office;
use Illuminate\View\View;
use Livewire\Attributes\Url;

new class extends Component {
    use WithPagination;

    #[Url(as: 'office', except: null)]
    public $selectedOfficeId = null;
    public $selectedOfficeName = null;

    public function mount()
    {
        if (!$this->selectedOfficeId) {
            $firstOffice = Office::first();
            if ($firstOffice) {
                $this->selectedOfficeId = $firstOffice->id;
                $this->selectedOfficeName = $firstOffice->name;
            }
        }
    }

    public function rendering(View $view): void
    {
        $view->title('Centros de costo');
    }

    public function resetForm()
    {
        $this->dispatch('reset-form-office');
    }

    #[On('close-office-modal')]
    public function closeModal()
    {
        $this->modal('create-office')->close();
    }

    #[On('delete-item.offices')]
    public function deleteItem($id)
    {
        try {
            $office = Office::findOrFail($id['id']);
            $office->delete();
            LivewireAlert::title('Centro de costo eliminado correctamente')->toast()->success()->position('top-end')->show();
            $this->dispatch('refresh-table.offices');
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    #[On('offices-row-selected')]
    public function handleOfficeSelection($itemId, $item)
    {
        $this->selectedOfficeId = $itemId;
        $this->selectedOfficeName = $item['name'];
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:heading size="xl">Gestión de centros de costo</flux:heading>
        <flux:text>Administra los centros de costo y las finalidades que estas tienen.</flux:text>
    </div>
    <!-- Modal de creacion -->
    <livewire:catalogs.office.create />
    <livewire:catalogs.office.subunit.create :officeId="$selectedOfficeId" />
    <!-- Modal de edicion -->
    <livewire:catalogs.office.edit />
    <livewire:catalogs.office.subunit.edit />
    <!-- Tabla de centros de costo -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <livewire:components.table 
        wire:key="offices-table-{{ $selectedOfficeId }}" 
        title="Centros de costo"
        description="Crea nuevos centros de costo habilitalos o deshabilitalos cuando sea necesario."
        :columns="[
                'code' => ['label' => 'Código', 'width' => 'w-18'],
                'name' => ['label' => 'Centro de costo', 'width' => 'w-56'],
                'status' => ['label' => 'Estado', 'width' => 'w-12 text-center'],
            ]" 
        :searchableColumns="['code', 'name']" 
        modelClass="App\Models\Office" 
        tableId="offices" 
        :perPage="20"
        :showActions="true" 
        :canEdit="true" 
        :canEnableDisable="true" 
        :showButton="true" 
        :buttonName="'Nuevo centro de costo'" 
        :buttonIcon="'plus-circle'"
        :isSelectable="true" 
        :selectedRowId="$selectedOfficeId" 
        :isSearchable="true" />
        <!-- tabla de subunidades/finalidades -->
        <livewire:components.table 
        wire:key="subunits-table-{{ $selectedOfficeId }}" 
        subtitle="{{ $selectedOfficeName }}"
        title="Finalidades"
        description="Crea Finalidades para en centro de costo seleccionado de la tabla centros de costos."
        :columns="[
                'name' => ['label' => 'Finalidad', 'width' => 'w-64'],
                'status' => ['label' => 'Estado', 'width' => 'w-18'],
            ]" 
        :searchableColumns="['name']" 
        modelClass="App\Models\Subunit" 
        tableId="subunits" 
        :filters="['office_id' => $selectedOfficeId]"
        :showActions="true" 
        :canEdit="true" 
        :canEnableDisable="true" 
        :showButton="true" 
        :buttonName="'Nueva finalidad'"
        :buttonIcon="'plus-circle'" />
    </div>
</div>
