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

    // Secciones principales (roles: reception, manager, admin)
    Route::middleware('role:reception,manager,admin')->group(function () {
        Route::get('/agenda', [AppointmentsController::class, 'index'])->name('appointments.index');
        Route::post('/agenda', [AppointmentsController::class, 'store'])->name('appointments.store');
        Route::post('/agenda/{appointment}/reschedule', [AppointmentsController::class, 'reschedule'])->name('appointments.reschedule');
        Route::post('/agenda/{appointment}/cancel', [AppointmentsController::class, 'cancel'])->name('appointments.cancel');
        Route::post('/agenda/{appointment}/attend', [AppointmentsController::class, 'attend'])->name('appointments.attend');
        Route::post('/agenda/{appointment}/no-show', [AppointmentsController::class, 'noShow'])->name('appointments.no_show');
        Route::get('/clientes', [ContactsController::class, 'index'])->name('contacts.index');
        Route::get('/llamadas', [CallsController::class, 'index'])->name('calls.index');
    });

    // Leads y mensajería (roles: manager, admin)
    Route::middleware('role:manager,admin')->group(function () {
        Route::get('/leads', [LeadsController::class, 'index'])->name('leads.index');
        Route::get('/mensajeria', [MessagingController::class, 'index'])->name('messaging.index');
        Route::get('/mensajeria/plantillas/{template}/preview', [MessagingController::class, 'preview'])->name('messaging.preview');
        Route::post('/mensajeria/plantillas/{template}/send', [MessagingController::class, 'send'])->name('messaging.send');
        Route::get('/informes', [ReportsController::class, 'index'])->name('reports.index');
    });

    // Integraciones y ajustes (solo admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/integraciones', [IntegrationsController::class, 'index'])->name('integrations.index');
        Route::get('/ajustes', [SettingsController::class, 'index'])->name('settings.index');
        // Ajustes específicos
        Route::get('/settings/feature-flags', [SettingsController::class, 'featureFlags'])->name('settings.feature-flags');
    });
});
