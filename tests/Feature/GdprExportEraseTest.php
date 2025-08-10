<?php

use App\Models\Customer;
use App\Models\User;

it('can export a demo customer via service', function () {
    $customer = Customer::factory()->create([
        'name' => 'Demo Person',
        'email' => 'demo@example.com',
        'phone' => '600000000',
    ]);

    $this->actingAs(User::factory()->create());

    $response = $this->get(route('contacts.export', $customer));
    $response->assertStatus(200);
    $response->assertHeader('content-disposition');
});

it('anonymizes personal data on erase', function () {
    $customer = Customer::factory()->create([
        'name' => 'Erase Me',
        'email' => 'erase@example.com',
        'phone' => '600000001',
        'secondary_phone' => '700000001',
        'notes' => 'sensitive',
    ]);

    $this->actingAs(User::factory()->create());

    $response = $this->post(route('contacts.erase', $customer));
    $response->assertRedirect();

    $customer->refresh();
    expect($customer->name)->toStartWith('Anónimo #')
        ->and($customer->email)->toBeNull()
        ->and($customer->phone)->toBeNull()
        ->and($customer->secondary_phone)->toBeNull()
        ->and($customer->notes)->toBeNull();
});


