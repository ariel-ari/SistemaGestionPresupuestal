<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Document;
use Illuminate\View\View;

new class extends Component {
    use WithPagination;

    public $nameFilter = '';

    public function rendering(View $view): void
    {
        $view->title('Documentos');
    }

    #[On('close-document-modal')]
    public function closeModal()
    {
        $this->modal('create-document')->close();
    }

    #[On('delete-item.documents')]
    public function deleteItem($id)
    {
        try {
            $document = Document::findOrFail($id['id']);
            $document->delete();
            LivewireAlert::title('Documento eliminado correctamente')->toast()->success()->position('top-end')->show();
            $this->dispatch('refresh-table.documents');
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:heading size="xl">
            Documentos
        </flux:heading>
        <flux:text>Administra los documentos.</flux:text>
    </div>
    <!-- Modal de creacion -->
    <livewire:catalogs.document.create />
    <!-- Modal de edicion -->
    <livewire:catalogs.document.edit />
    <!-- Tabla de documentos -->
    <livewire:components.table title="Documentos" description="Administra los documentos." :columns="[
        'name' => ['label' => 'Documento', 'width' => 'w-64'],
        'status' => ['label' => 'Estado', 'width' => 'w-24'],
    ]" :searchableColumns="['name']" modelClass="App\Models\Document" tableId="documents"
        :perPage="20" :showActions="true" :canEdit="true" :canEnableDisable="true" :showButton="true" buttonName="Nuevo documento" />
</div>
