<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRM\DashboardController;
use App\Http\Controllers\CRM\ContactsController;
use App\Http\Controllers\CRM\DealsController;
use App\Http\Controllers\CRM\AppointmentsController;
use App\Http\Controllers\CRM\SettingsController;

Route::get('/', DashboardController::class)->name('dashboard');
Route::get('/contacts', [ContactsController::class, 'index'])->name('contacts.index');
Route::get('/deals', [DealsController::class, 'index'])->name('deals.index');
Route::get('/appointments', [AppointmentsController::class, 'index'])->name('appointments.index');
Route::get('/settings/feature-flags', [SettingsController::class, 'featureFlags'])->name('settings.feature-flags');
