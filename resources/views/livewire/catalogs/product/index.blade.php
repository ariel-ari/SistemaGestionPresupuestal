<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Product;
use Illuminate\View\View;

new class extends Component {
    use WithPagination;

    public $nameFilter = '';

    public function rendering(View $view): void
    {
        $view->title('Productos');
    }

    public function resetForm()
    {
        $this->dispatch('reset-form-product');
    }

    #[On('close-product-modal')]
    public function closeModal()
    {
        $this->modal('create-product')->close();
    }

    #[On('delete-item.products')]
    public function deleteItem($id)
    {
        try {
            $product = Product::findOrFail($id['id']);
            $product->delete();
            LivewireAlert::title('Producto eliminado correctamente')->toast()->success()->position('top-end')->show();
            $this->dispatch('refresh-table.products');
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }
}; ?>

<div>
    <div class="mb-4">
        <flux:heading size="xl">
            Productos
        </flux:heading>
        <flux:text>Administra los productos.</flux:text>
    </div>
    <livewire:catalogs.product.create />
    <livewire:catalogs.product.edit />
    <livewire:components.table title="Productos" description="Crea productos y habilita o deshabilita cuando sea necesario." :columns="[
        'code' => ['label' => 'Código', 'width' => 'w-24'],
        'name' => ['label' => 'Producto', 'width' => 'w-64'],
        'status' => ['label' => 'Estado', 'width' => 'w-64'],
    ]" :searchableColumns="['code', 'name']" modelClass="App\Models\Product" tableId="products"
        :perPage="20" :showActions="true" :canEdit="true" :canEnableDisable="true" :showButton="true" buttonName="Nuevo producto"/>
</div>
