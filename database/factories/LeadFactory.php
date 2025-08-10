<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->optional()->safeEmail(),
            'phone' => $this->faker->optional()->e164PhoneNumber(),
            'source' => $this->faker->randomElement(['website','facebook','google','referral']),
            'notes' => $this->faker->sentence(),
        ];
    }
}




