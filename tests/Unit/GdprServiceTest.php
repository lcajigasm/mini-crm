<?php

use App\Models\Customer;
use App\Support\Gdpr\GdprService;

it('records consent grant and revoke', function () {
    $service = app(GdprService::class);
    $customer = Customer::factory()->create();

    $grant = $service->recordConsent($customer, 'email', true, 'ui');
    expect($grant->granted)->toBeTrue()->and($grant->granted_at)->not->toBeNull();

    $revoke = $service->recordConsent($customer, 'email', false, 'ui');
    expect($revoke->granted)->toBeFalse()->and($revoke->revoked_at)->not->toBeNull();
});



