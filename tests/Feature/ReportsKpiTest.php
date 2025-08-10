<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsKpiTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_page_and_api_load(): void
    {
        $this->seed();

        $user = User::where('email', 'admin@example.com')->first();
        $this->actingAs($user);

        $this->get(route('reports.index'))
            ->assertStatus(200)
            ->assertSee('Informes');

        $this->get('/api/reports/kpis')
            ->assertOk()
            ->assertJsonStructure([
                'filters' => ['source'],
                'totals' => [
                    'leads_24h','leads_7d','appointment_rate_7d','attendance_rate_7d','conversion_rate_7d','no_show_rate_7d','sessions_completed_7d'
                ],
                'series_7d',
            ]);
    }
}



