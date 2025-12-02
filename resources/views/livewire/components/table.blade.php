<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use WithPagination;

    public ?string $title = '';
    public ?string $subtitle = '';
    public ?string $description = '';
    public array $columns = [];
    public string $tableId = 'default';
    public string $modelClass = '';
    public array $filters = [];
    public string $scopeMethod = '';
    public int $perPage = 10;
    public $data = []; // Nombre en URL
    // Mantener en navegación
    // Agregar al historial del navegador
    // No agregar a URL si está vacío

    //Busqueda
    public string $search = '';
    public bool $isSearchable = false;

    // Columnas buscables: ['field' => 'direct'] para columnas directas, ['field' => 'relation.field'] para relaciones
    public array $searchableColumns = [];

    // Relaciones a cargar con eager loading (evita N+1)
    public array $eagerLoad = [];

    //Botones
    public bool $showButton = false;
    public string $buttonName = '';
    public string $buttonIcon = 'plus-circle';

    // Acciones
    public bool $showActions = false; // Mostrar columna de acciones
    public bool $canEdit = false;
    public bool $canDelete = false;
    public bool $canEnableDisable = false;
    public bool $isSelectable = false;
    public ?int $selectedRowId = null;

    // Formatos de columnas
    public array $columnFormats = []; // ['price' => 'money', 'date' => 'date']

    protected function queryString()
    {
        return [
            'search' => [
                'as' => $this->tableId . '_q',
                'except' => '',
                'history' => false,
            ],
        ];
    }

    #[On('refresh-table.{tableId}')]
    public function refreshTable(): void
    {
        $this->resetPage($this->tableId . 'Page');
    }

    #[On('refresh-table.{tableId}')]
    public function with(): array
    {
        try {
            if (empty($this->modelClass) || !class_exists($this->modelClass)) {
                return ['items' => collect()->paginate($this->perPage)];
            }

            $query = $this->modelClass::query();

            if (!empty($this->scopeMethod) && method_exists($this->modelClass, 'scope' . ucfirst($this->scopeMethod))) {
                $query = $query->{$this->scopeMethod}(...array_values($this->filters));
            } else {
                foreach ($this->filters as $field => $value) {
                    if (!empty($value)) {
                        if (is_array($value)) {
                            $query->whereIn($field, $value);
                        } else {
                            $query->where($field, $value);
                        }
                    }
                }
            }

            if (!empty($this->search)) {
                $query->where(function ($q) {
                    // Determinar qué columnas buscar
                    $columnsToSearch = !empty($this->searchableColumns) ? $this->searchableColumns : array_keys($this->columns);

                    foreach ($columnsToSearch as $field) {
                        // Si contiene un punto, es una relación (ej: 'roles.name')
                        if (str_contains($field, '.')) {
                            [$relation, $relationField] = explode('.', $field, 2);
                            $q->orWhereHas($relation, function ($query) use ($relationField) {
                                $query->where($relationField, 'like', '%' . $this->search . '%');
                            });
                        } else {
                            // Es una columna directa
                            $q->orWhere($field, 'like', '%' . $this->search . '%');
                        }
                    }
                });
            }

            // Aplicar eager loading si se especificó
            if (!empty($this->eagerLoad)) {
                $query->with($this->eagerLoad);
            }

            return [
                'items' => $query->paginate($this->perPage, ['*'], $this->tableId . 'Page'),
            ];
        } catch (\Throwable $th) {
            LivewireAlert::title('Error')->toast()->error()->position('top-end')->show();
            return ['items' => collect()->paginate($this->perPage)];
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage($this->tableId . 'Page');
    }

    public function deleteConfirm($id): void
    {
        LivewireAlert::title('Eliminar registro?')
            ->text('¿Está seguro de eliminar el registro? No podrá deshacer esta acción.')
            ->asConfirm()
            ->onConfirm('deleteItem', ['id' => $id])
            ->show();
    }

    public function editItem($id): void
    {
        $this->dispatch('edit-item.' . $this->tableId, id: $id);
    }

    public function deleteItem($id): void
    {
        $this->dispatch('delete-item.' . $this->tableId, id: $id);
    }
}; ?>

<div>
    <div class="mb-2">
        <div class="flex items-center justify-between mb-3">
            <div>
                @if ($title)
                    <flux:heading size="lg">{{ $title }}</flux:heading>
                    <flux:text>{{ $description }}</flux:text>
                @endif
            </div>
            @if ($showButton)
                <flux:modal.trigger name="create-{{ $tableId }}">
                    <flux:button class="cursor-pointer" size="sm" icon="{{ $buttonIcon }}" variant="primary">
                        {{ $buttonName }}</flux:button>
                </flux:modal.trigger>
            @endif
        </div>
        @if ($subtitle)
            <flux:heading size="lg">{{ $subtitle }}</flux:heading>
        @endif
        @if ($isSearchable)
            <flux:input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar en tabla..." />
        @endif
    </div>


    <div
        class="relative overflow-x-auto bg-white dark:bg-zinc-800 shadow-lg rounded-lg border border-gray-200 dark:border-zinc-700">
        <table class="table-fixed w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-50 dark:bg-zinc-900 border-b border-gray-100 dark:border-zinc-700">
                <tr>
                    @foreach ($columns as $field => $column)
                        <th scope="col"
                            class="px-2 py-2 font-semibold text-gray-700 dark:text-gray-300 tracking-wider {{ is_array($column) ? $column['width'] ?? '' : '' }}">
                            {{ is_array($column) ? $column['label'] : $column }}
                        </th>
                    @endforeach

                    @if ($showActions)
                        <th scope="col"
                            class="w-22 font-semibold text-gray-700 dark:text-gray-300 tracking-wider text-center">
                            Acciones
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse ($items as $item)
                    <livewire:components.table-row
                        wire:key="row-{{ $tableId }}-{{ $item->id }}-{{ $item->updated_at }}"
                        :item="$item" :columns="$columns" :showActions="$showActions" :canEdit="$canEdit" :canDelete="$canDelete"
                        :columnFormats="$columnFormats" :tableId="$tableId" :canEnableDisable="$canEnableDisable" :isSelectable="$isSelectable" :selectedRowId="$selectedRowId" />
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + ($showActions ? 1 : 0) }}"
                            class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No hay registros
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $items->links() }}
    </div>
</div>
