<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');


Route::view('/', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('home');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    //Usuarios
    Volt::route('users', 'users.index')->name('users');
    //Catalogs
    Volt::route('catalogs/office', 'catalogs.office.index')->name('catalogs.office');
    Volt::route('catalogs/purpose', 'catalogs.purpose.index')->name('catalogs.purpose');
    Volt::route('catalogs/activity', 'catalogs.activity.index')->name('catalogs.activity');
    Volt::route('catalogs/financing', 'catalogs.financing.index')->name('catalogs.financing');
    Volt::route('catalogs/classifier', 'catalogs.classifier.index')->name('catalogs.classifier');
    Volt::route('catalogs/product', 'catalogs.product.index')->name('catalogs.product');
    Volt::route('catalogs/budget_program', 'catalogs.budgetprogram.index')->name('catalogs.budget_program');
    Volt::route('catalogs/goal', 'catalogs.goal.index')->name('catalogs.goal');
    Volt::route('catalogs/document','catalogs.document.index')->name('catalogs.document');
});
