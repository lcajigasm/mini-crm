<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('denies access to manager-only routes for reception (403)', function () {
    $user = User::factory()->create(['role' => 'reception']);
    $this->actingAs($user);

    $this->get(route('leads.index'))->assertForbidden();
    $this->get(route('messaging.index'))->assertForbidden();
    $this->get(route('reports.index'))->assertForbidden();
});

it('denies access to admin-only routes for manager (403)', function () {
    $user = User::factory()->create(['role' => 'manager']);
    $this->actingAs($user);

    $this->get(route('integrations.index'))->assertForbidden();
    $this->get(route('settings.index'))->assertForbidden();
    $this->get(route('settings.feature-flags'))->assertForbidden();
});

it('allows reception to access reception routes', function () {
    $user = User::factory()->create(['role' => 'reception']);
    $this->actingAs($user);

    $this->get(route('appointments.index'))->assertOk();
    $this->get(route('contacts.index'))->assertOk();
    $this->get(route('calls.index'))->assertOk();
});

it('admin can access everything', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);

    $this->get(route('appointments.index'))->assertOk();
    $this->get(route('contacts.index'))->assertOk();
    $this->get(route('calls.index'))->assertOk();
    $this->get(route('leads.index'))->assertOk();
    $this->get(route('messaging.index'))->assertOk();
    $this->get(route('reports.index'))->assertOk();
    $this->get(route('integrations.index'))->assertOk();
    $this->get(route('settings.index'))->assertOk();
    $this->get(route('settings.feature-flags'))->assertOk();
});




