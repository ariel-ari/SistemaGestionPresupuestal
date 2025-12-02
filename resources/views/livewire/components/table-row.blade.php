<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

new class extends Component {
    public $item;
    public array $columns = [];
    public bool $showActions = false;
    public bool $canEdit = false;
    public bool $canDelete = false;
    public array $columnFormats = [];
    public string $tableId = 'default';
    public bool $canEnableDisable = false;

    public bool $isSelectable = false;
    public ?int $selectedRowId = null;

    public function formatValue($field, $value)
    {
        $format = $this->columnFormats[$field] ?? null;

        return match ($format) {
            'money' => 'S/ ' . number_format($value, 2, '.', ','),
            'date' => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '',
            'datetime' => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y H:i') : '',
            'boolean' => $value ? 'Deshabilitado' : 'Habilitado',
            default => $value,
        };
    }

    public function toggleEnableDisable($id)
    {
        try {
            $this->item->is_active = !$this->item->is_active;
            $this->item->save();

            LivewireAlert::title('Estado actualizado')->text('El estado del registro ha sido actualizado correctamente.')->toast()->position('bottom-end')->success()->show();
        } catch (\Throwable $th) {
            LivewireAlert::title('Error')->text('Ha ocurrido un error al actualizar el estado del registro.')->toast()->position('bottom-end')->error()->show();
        }
    }

    public function selectRow()
    {
        if($this->isSelectable) {
            $this->dispatch($this->tableId . '-row-selected', itemId: $this->item->id, item: $this->item->toArray());
        }
    }

}; ?>

<tr
    @if($isSelectable) 
        wire:click="selectRow" 
        class="cursor-pointer {{ $selectedRowId === $item->id ? 'bg-blue-100 dark:bg-blue-900/30 border-l-4 border-blue-500' : 'bg-white dark:bg-zinc-800' }} hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition-colors border-b border-gray-100 dark:border-zinc-700"
    @else
        class="bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition-colors border-b border-gray-100 dark:border-zinc-700"
    @endif>
    @foreach ($columns as $field => $column)
        <td class="px-1 py-1 text-gray-900 dark:text-gray-100">
            @if ($field == 'status')
                @if ($item->is_active)
                    <flux:badge size="sm" variant="pill" color="green">Habilitado</flux:badge>
                @else
                    <flux:badge size="sm" variant="pill" color="red">Deshabilitado</flux:badge>
                @endif
            @else
                <flux:text size="sm" >{{ $this->formatValue($field, data_get($item, $field)) }}</flux:text>
            @endif
        </td>
    @endforeach
        
    @if ($showActions)
        <td class="text-center">
            <div class="flex items-center justify-center gap-1">
                @if ($canEnableDisable)
                    <flux:button size="xs" class="cursor-pointer"
                        wire:click.stop="toggleEnableDisable({{ $item->id }})" variant="primary" color="{{ $item->is_active ? 'red' : 'green' }}">
                        {{ $item->is_active ? 'Deshabilitar' : 'Habilitar' }}
                    </flux:button>
                @endif
                @if ($canEdit)
                    <flux:button size="xs" class="cursor-pointer"
                        wire:click.stop="$parent.editItem({{ $item->id }})" variant="primary">Editar
                    </flux:button>
                @endif
                @if ($canDelete)
                    <flux:button size="xs" class="cursor-pointer" icon="trash"
                        wire:click.stop="$parent.deleteConfirm({{ $item->id }})" variant="danger">Eliminar
                    </flux:button>
                @endif
            </div>
        </td>
    @endif
</tr>
