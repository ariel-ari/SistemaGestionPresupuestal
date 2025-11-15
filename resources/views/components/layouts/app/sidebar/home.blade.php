<flux:navlist.item icon="home" :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
    {{ __('Panel') }}</flux:navlist.item>
<flux:navlist.item href="#" icon="inbox" badge="12">Cambios solicitados</flux:navlist.item>
<flux:navlist.item href="#" icon="users">Usuarios</flux:navlist.item>
