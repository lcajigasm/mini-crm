<?php

use App\Support\TemplateRenderer;

it('renders variables with fallback', function () {
    $text = 'Hola {{name|cliente}}, cita {{appointment.date|hoy}}';
    $out = TemplateRenderer::render($text, ['name' => 'Ana']);
    expect($out)->toBe('Hola Ana, cita hoy');
});

it('supports dot notation', function () {
    $text = 'Hola {{customer.name|cliente}}';
    $out = TemplateRenderer::render($text, ['customer' => ['name' => 'Luis']]);
    expect($out)->toBe('Hola Luis');
});


