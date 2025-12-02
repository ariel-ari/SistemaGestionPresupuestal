<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\FinancingForm;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public FinancingForm $form;

    #[On('reset-form-financing')]
    public function resetFormFinancing()
    {
        $this->resetForm();
    }

    public function save()
    {
        try {
            $this->form->store();
            Flux::modal('create-financings')->close();
            $this->dispatch('refresh-table.financings');
            $this->resetForm();
            LivewireAlert::title('Fuente de financiamiento agregada')->text('La fuente de financiamiento se agrego correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }
}; ?>

<flux:modal @close="resetFormFinancing" name="create-financings" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Agregar fuente de financiamiento</flux:heading>
    <flux:text>Complete el codigo y el nombre de la fuente de financiamiento.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.code" label="Codigo" type="text" required placeholder="Codigo" />
        <flux:input wire:model="form.name" label="Fuente de financiamiento" type="text" required
            placeholder="Fuente de financiamiento" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Agregar</flux:button>
        </div>
    </form>
</flux:modal>
