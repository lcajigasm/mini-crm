<?php

namespace Database\Factories;

use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Template> */
class TemplateFactory extends Factory
{
    protected $model = Template::class;

    public function definition(): array
    {
        return [
            'key' => 'tpl_' . $this->faker->unique()->lexify('????'),
            'name' => $this->faker->sentence(2),
            'channel' => 'both',
            'whatsapp_template' => null,
            'subject' => null,
            'content_text' => 'Hola {{customer.name|cliente}}',
            'content_html' => null,
            'variables' => ['customer.name'],
            'active' => true,
        ];
    }
}


