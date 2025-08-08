<?php

use App\Models\Template;
use App\Models\User;
use function Pest\Laravel\actingAs;

it('shows templates and allows enqueue send', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $template = Template::factory()->create([
        'key' => 'test_tpl',
        'name' => 'Test',
        'channel' => 'whatsapp',
        'content_text' => 'Hola {{customer.name|cliente}}',
    ]);

    actingAs($user)
        ->get(route('messaging.index'))
        ->assertOk()
        ->assertSee('Test');
});


