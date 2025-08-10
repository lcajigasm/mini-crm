<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\Treatment;
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

        // Leads in preventa pipeline with explicit sources
        $preventa = Pipeline::where('slug', 'preventa')->first();
        $firstStage = $preventa ? $preventa->stages()->orderBy('display_order')->first() : null;
        $sources = ['google','meta','organic'];
        $leads = collect();
        foreach ($sources as $s) {
            $leads = $leads->merge(Lead::factory()->count(4)->create([
                'pipeline_id' => $preventa?->id,
                'stage_id' => $firstStage?->id,
                'source' => $s,
            ]));
        }

        // Appointments: some today, some this week, including no-shows
        $today = now()->startOfDay();
        foreach ($customers->take(5) as $index => $customer) {
            Appointment::create([
                'customer_id' => $customer->id,
                'scheduled_at' => $today->copy()->addHours(9 + $index),
                'status' => $index % 3 === 0 ? 'attended' : 'scheduled',
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

        // Conversions: create some treatments from recent leads and mark 6 attended sessions for a subset
        $recentLeads = $leads->take(3);
        foreach ($recentLeads as $lead) {
            $treatment = Treatment::create([
                'lead_id' => $lead->id,
                'customer_id' => $customers->random()->id,
                'sessions_count' => 6,
                'started_at' => now()->subDays(5),
            ]);

            // 6 attended sessions finishing within last 7 days for first 2 leads
            $sessions = 6;
            $finishDaysAgo = $lead->id % 2 === 0 ? 1 : 3; // scatter completion
            for ($i = 1; $i <= $sessions; $i++) {
                Appointment::create([
                    'customer_id' => $treatment->customer_id,
                    'lead_id' => $lead->id,
                    'treatment_id' => $treatment->id,
                    'scheduled_at' => now()->subDays($finishDaysAgo + (6 - $i))->setTime(9 + ($i % 3) * 2, 0),
                    'status' => 'attended',
                    'session_number' => $i,
                    'location' => 'Clínica Centro',
                ]);
            }
        }
    }
}



