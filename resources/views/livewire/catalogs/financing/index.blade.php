<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Financing;
use Illuminate\View\View;

new class extends Component {
    use WithPagination;

    public $nameFilter = '';

    public function rendering(View $view): void
    {
        $view->title('Fuentes de financiamiento');
    }

    public function resetForm()
    {
        $this->dispatch('reset-form-financing');
    }

    #[On('close-financing-modal')]
    public function closeModal()
    {
        $this->modal('create-financing')->close();
    }

    #[On('delete-item.financings')]
    public function deleteItem($id)
    {
        try {
            $financing = Financing::findOrFail($id['id']);
            $financing->delete();
            LivewireAlert::title('Fuente de financiamiento eliminada correctamente')->toast()->success()->position('top-end')->show();
            $this->dispatch('refresh-table.financings');
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:heading size="xl">
            Fuentes de financiamiento
        </flux:heading>
        <flux:text>Administra las fuentes de financiamiento.</flux:text>
    </div>
    <livewire:catalogs.financing.create />
    <livewire:catalogs.financing.edit />
    <livewire:components.table title="Fuentes de financiamiento" description="Crea nuevas fuentes de financiamiento y habilitalos o deshabilitalos cuando sea necesario." :columns="[
        'code' => ['label' => 'Código', 'width' => 'w-24'],
        'name' => ['label' => 'Fuente de financiamiento', 'width' => 'w-64'],
        'status' => ['label' => 'Estado', 'width' => 'w-24'],
    ]" :searchableColumns="['code', 'name']" modelClass="App\Models\Financing" tableId="financings"
        :perPage="20" :showActions="true" :canEdit="true" :canEnableDisable="true" :showButton="true" :buttonName="'Nueva fuente de financiamiento'" />
</div>
