<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\DocumentForm;
use App\Models\Document;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public DocumentForm $form;

    #[On('edit-item.documents')]
    public function editItem($id)
    {
        try {
            $this->form->setModel(Document::findOrFail($id));
            Flux::modal('edit-document')->show();
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    public function save()
    {
        try {
            $this->form->update();
            Flux::modal('edit-document')->close();
            $this->dispatch('refresh-table.documents');
            LivewireAlert::title('Documento actualizado')->text('El documento se actualizo correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }

    public function resetFormDocument()
    {
        $this->resetForm();
    }
}; ?>


<flux:modal @close="resetFormDocument" name="edit-document" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Editar documento</flux:heading>
    <flux:text>Edita los campos que sean necesarios.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.name" label="Documento" type="text" required
            placeholder="Documento" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Guardar Cambios</flux:button>
        </div>
    </form>
</flux:modal>
