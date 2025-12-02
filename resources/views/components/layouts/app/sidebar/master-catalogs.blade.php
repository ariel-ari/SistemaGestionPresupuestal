<flux:navlist.group heading="Catálogos maestros" expandable
    :expanded="request()->routeIs('catalogs.office', 'catalogs.purpose', 'catalogs.activity', 'catalogs.financing','catalogs.classifier', 'catalogs.product','catalogs.budget_program', 'catalogs.goal','catalogs.document') ? true : false">

    <flux:navlist.item :href="route('catalogs.office')" :current="request()->routeIs('catalogs.office')" wire:navigate>
        Centros de costo
    </flux:navlist.item>
    <flux:navlist.item :href="route('catalogs.financing')" :current="request()->routeIs('catalogs.financing')"
        wire:navigate>Fuentes de financiamiento</flux:navlist.item>
    <flux:navlist.item :href="route('catalogs.classifier')" :current="request()->routeIs('catalogs.classifier')"
        wire:navigate>Clasificadores de gastos</flux:navlist.item>
    <flux:navlist.item :href="route('catalogs.activity')" :current="request()->routeIs('catalogs.activity')"
        wire:navigate>
        Actividades
    </flux:navlist.item>
    <flux:navlist.item :href="route('catalogs.purpose')" :current="request()->routeIs('catalogs.purpose')"
        wire:navigate>
        Finalidades públicas
    </flux:navlist.item>
    <flux:navlist.item :href="route('catalogs.product')" :current="request()->routeIs('catalogs.product')"
        wire:navigate>
        Productos
    </flux:navlist.item>
    <flux:navlist.item :href="route('catalogs.budget_program')"
        :current="request()->routeIs('catalogs.budget_program')" wire:navigate>
        Programas presupuestales
    </flux:navlist.item>
    <flux:navlist.item :href="route('catalogs.goal')" :current="request()->routeIs('catalogs.goal')" wire:navigate>
        Metas
    </flux:navlist.item>
    <flux:navlist.item :href="route('catalogs.document')" :current="request()->routeIs('catalogs.document')"
        wire:navigate>
        Documentos
    </flux:navlist.item>
</flux:navlist.group>
