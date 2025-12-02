<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Purpose;
use Illuminate\View\View;

new class extends Component {
    use WithPagination;

    public $nameFilter = '';

    public function rendering(View $view): void
    {
        $view->title('Finalidades Públicas');
    }

    public function resetForm()
    {
        $this->dispatch('reset-form-purpose');
    }

    #[On('close-purpose-modal')]
    public function closeModal()
    {
        $this->modal('create-purpose')->close();
    }

    #[On('delete-item.purposes')]
    public function deleteItem($id)
    {
        try {
            $purpose = Purpose::findOrFail($id['id']);
            $purpose->delete();
            LivewireAlert::title('Finalidad Pública eliminada correctamente')->toast()->success()->position('top-end')->show();
            $this->dispatch('refresh-table.purposes');
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:heading size="xl">
            Finalidades Públicas
        </flux:heading>
        <flux:text>Administra las finalidades públicas</flux:text>
    </div>
    <livewire:catalogs.purpose.create />
    <livewire:catalogs.purpose.edit />
    <livewire:components.table 
    title="Finalidades Públicas" 
    description="Crea nuevas finalidades públicas y habilitalas o deshabilitalas cuando sea necesario" 
    :columns="[
        'name' => ['label' => 'Finalidad pública', 'width' => 'w-64'],
        'status' => ['label' => 'Estado', 'width' => 'w-64'],
    ]" 
    :searchableColumns="['name']"
    modelClass="App\Models\Purpose" 
    tableId="purposes" 
    :perPage="20" 
    :showActions="true" 
    :canEdit="true" 
    :canEnableDisable="true" 
    :showButton="true" 
    :buttonName="'Nueva finalidad pública'"
    :isSearchable="true"/>
</div>
