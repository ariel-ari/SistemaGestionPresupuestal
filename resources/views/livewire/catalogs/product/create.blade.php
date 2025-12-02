<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\ProductForm;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public ProductForm $form;

    #[On('reset-form-product')]
    public function resetFormProduct()
    {
        $this->resetForm();
    }

    public function save()
    {
        try {
            $this->form->store();
            Flux::modal('create-products')->close();
            $this->dispatch('refresh-table.products');
            $this->resetForm();
            LivewireAlert::title('Producto agregado')->text('El producto se agrego correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }
}; ?>

<flux:modal @close="resetFormProduct" name="create-products" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Agregar producto</flux:heading>
    <flux:text>Complete el codigo y el nombre del producto.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.code" label="Codigo" type="text" required placeholder="Codigo" />
        <flux:input wire:model="form.name" label="Producto" type="text" required
            placeholder="Producto" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Agregar</flux:button>
        </div>
    </form>
</flux:modal>
