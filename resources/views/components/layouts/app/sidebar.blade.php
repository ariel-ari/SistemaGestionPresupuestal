<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
        <x-app-logo />
        <livewire:sidebar.year-selector />
        <flux:navlist variant="outline">
            <x-layouts.app.sidebar.home />
            <x-layouts.app.sidebar.operations />
            <x-layouts.app.sidebar.master-catalogs />
            <x-layouts.app.sidebar.consultations />
            <x-layouts.app.sidebar.reports />
        </flux:navlist>

        <flux:spacer />
        <x-layouts.app.sidebar.profile-desktop />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <x-layouts.app.sidebar.profile-mobile />
    {{ $slot }}

    @fluxScripts
</body>

</html>
