<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'scheduled_at' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'duration_minutes' => $this->faker->randomElement([30, 45, 60]),
            'status' => $this->faker->randomElement(['scheduled','rescheduled','cancelled','attended','no_show']),
            'session_number' => $this->faker->numberBetween(1, 6),
            'location' => $this->faker->randomElement(['Clínica Centro','Clínica Norte']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}


