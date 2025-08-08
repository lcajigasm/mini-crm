<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Customers and appointments
        $customers = Customer::factory()->count(10)->create();

        // Leads in preventa pipeline
        $preventa = Pipeline::where('slug', 'preventa')->first();
        $firstStage = $preventa ? $preventa->stages()->orderBy('display_order')->first() : null;
        Lead::factory()->count(5)->create([
            'pipeline_id' => $preventa?->id,
            'stage_id' => $firstStage?->id,
        ]);

        // Appointments: some today, some this week, including a couple of no-shows
        $today = now()->startOfDay();
        foreach ($customers->take(5) as $index => $customer) {
            Appointment::create([
                'customer_id' => $customer->id,
                'scheduled_at' => $today->copy()->addHours(9 + $index),
                'status' => 'scheduled',
                'session_number' => ($index % 6) + 1,
                'location' => 'Clínica Centro',
            ]);
        }

        // No-show events this week
        foreach ($customers->slice(5, 2) as $customer) {
            Appointment::create([
                'customer_id' => $customer->id,
                'scheduled_at' => now()->subDays(rand(1, 3))->setTime(10, 0),
                'status' => 'no_show',
                'session_number' => 1,
                'location' => 'Clínica Norte',
            ]);
        }
    }
}


