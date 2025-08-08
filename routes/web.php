<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRM\DashboardController;
use App\Http\Controllers\CRM\ContactsController;
use App\Http\Controllers\CRM\DealsController;
use App\Http\Controllers\CRM\AppointmentsController;
use App\Http\Controllers\CRM\SettingsController;
use App\Http\Controllers\CRM\LeadsController;
use App\Http\Controllers\CRM\MessagingController;
use App\Http\Controllers\CRM\CallsController;
use App\Http\Controllers\CRM\IntegrationsController;
use App\Http\Controllers\CRM\ReportsController;

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    // Secciones principales
    Route::get('/agenda', [AppointmentsController::class, 'index'])->name('appointments.index');
    Route::post('/agenda', [AppointmentsController::class, 'store'])->name('appointments.store');
    Route::post('/agenda/{appointment}/reschedule', [AppointmentsController::class, 'reschedule'])->name('appointments.reschedule');
    Route::post('/agenda/{appointment}/cancel', [AppointmentsController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/agenda/{appointment}/attend', [AppointmentsController::class, 'attend'])->name('appointments.attend');
    Route::post('/agenda/{appointment}/no-show', [AppointmentsController::class, 'noShow'])->name('appointments.no_show');
    Route::get('/leads', [LeadsController::class, 'index'])->name('leads.index');
    Route::get('/clientes', [ContactsController::class, 'index'])->name('contacts.index');
    Route::get('/mensajeria', [MessagingController::class, 'index'])->name('messaging.index');
    Route::get('/llamadas', [CallsController::class, 'index'])->name('calls.index');
    Route::get('/integraciones', [IntegrationsController::class, 'index'])->name('integrations.index');
    Route::get('/ajustes', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/informes', [ReportsController::class, 'index'])->name('reports.index');

    // Ajustes específicos
    Route::get('/settings/feature-flags', [SettingsController::class, 'featureFlags'])->name('settings.feature-flags');
});
