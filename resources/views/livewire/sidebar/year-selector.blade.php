<?php

use Livewire\Volt\Component;

new class extends Component {
    public int $year = 2025;
}; ?>

<div>
    <flux:label>Año fiscal</flux:label>
    <flux:select class="cursor-pointer" wire:model.live='year'>
        <flux:select.option>2024</flux:select.option>
        <flux:select.option>2025</flux:select.option>
        <flux:select.option>2026</flux:select.option>
    </flux:select>
</div>
