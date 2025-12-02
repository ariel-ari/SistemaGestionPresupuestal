<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\ActivityForm;
use App\Models\Activity;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Traits\ResetsForm;

new class extends Component {

    use ResetsForm;

    public ActivityForm $form;

    #[On('edit-item.activities')]
    public function editItem($id)
    {
        try {
            $this->form->setModel(Activity::findOrFail($id));
            Flux::modal('edit-activity')->show();
        } catch (\Throwable $th) {
            LivewireAlert::title('Ups! Ocurrió un error. No encontramos el dato solicitado. Por favor, recarga la página.')->toast()->error()->position('top-end')->show();
        }
    }

    public function save()
    {
        try {
            $this->form->update();
            Flux::modal('edit-activity')->close();
            $this->dispatch('refresh-table.activities');
            LivewireAlert::title('Actividad actualizada')->text('La actividad se actualizo correctamente')->toast()->position('top-end')->success()->show();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            LivewireAlert::title('Ocurrio un Error')->text($e->getMessage())->toast()->position('top-end')->error()->show();
        }
    }

    public function resetFormActivity()
    {
        $this->resetForm();
    }
}; ?>


<flux:modal @close="resetFormActivity" name="edit-activity" class="grid gap-2 md:w-96">
    <flux:heading class="mt-2" size="lg">Editar actividad</flux:heading>
    <flux:text>Edita los campos que sean necesarios.</flux:text>
    <form wire:submit='save' class="grid gap-2">
        <flux:input wire:model="form.name" label="Actividad" type="text" required
            placeholder="Actividad" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Guardar Cambios</flux:button>
        </div>
    </form>
</flux:modal>
