<?php

namespace App\Domain\Services;

use App\Models\Appointment;
use App\Models\Lead;
use App\Models\Treatment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportsService
{
    /**
     * Build KPIs and a 7-day daily series, optionally filtered by lead source.
     *
     * @param string|null $source One of 'google', 'meta', 'organic' or null for all
     * @return array
     */
    public function buildKpis(?string $source = null): array
    {
        $now = Carbon::now();
        $since24h = $now->copy()->subDay();
        $since7d = $now->copy()->subDays(7);

        // Base lead query with optional source
        $leadBase = Lead::query();
        if ($source !== null && $source !== '') {
            $leadBase->where('source', $source);
        }

        // Leads created
        $leads24h = (clone $leadBase)->where('created_at', '>=', $since24h)->count();
        $leads7d = (clone $leadBase)->where('created_at', '>=', $since7d)->count();

        // Appointment rate (7d): leads created in last 7d that also have an appointment created in last 7d
        $leadsWithAppt7d = (clone $leadBase)
            ->where('leads.created_at', '>=', $since7d)
            ->join('appointments', function ($join) use ($since7d) {
                $join->on('appointments.lead_id', '=', 'leads.id')
                    ->where('appointments.created_at', '>=', $since7d);
            })
            ->distinct('leads.id')
            ->count('leads.id');
        $appointmentRate7d = $leads7d > 0 ? ($leadsWithAppt7d / $leads7d) : 0.0;

        // Attendance metrics (7d) for appointments scheduled in last 7d
        $apptBase = Appointment::query()
            ->where('scheduled_at', '>=', $since7d);
        if ($source !== null && $source !== '') {
            $apptBase->whereIn('lead_id', function ($q) use ($source) {
                $q->select('id')->from('leads')->where('source', $source);
            });
        }

        $attended7d = (clone $apptBase)->where('status', 'attended')->count();
        $noShow7d = (clone $apptBase)->where('status', 'no_show')->count();
        $cancelled7d = (clone $apptBase)->where('status', 'cancelled')->count();
        $attendanceDen = $attended7d + $noShow7d + $cancelled7d;
        $attendanceRate7d = $attendanceDen > 0 ? ($attended7d / $attendanceDen) : 0.0;
        $noShowRate7d = $attendanceDen > 0 ? ($noShow7d / $attendanceDen) : 0.0;

        // Conversion to sale (7d): leads created in last 7d that have a treatment created in last 7d
        $leadsWithTreatment7d = (clone $leadBase)
            ->where('leads.created_at', '>=', $since7d)
            ->join('treatments', function ($join) use ($since7d) {
                $join->on('treatments.lead_id', '=', 'leads.id')
                    ->where('treatments.created_at', '>=', $since7d);
            })
            ->distinct('leads.id')
            ->count('leads.id');
        $conversionRate7d = $leads7d > 0 ? ($leadsWithTreatment7d / $leads7d) : 0.0;

        // Sessions completed 6/6 in last 7d: treatments whose attended appointments reached sessions_count within last 7d
        // Count treatments that have at least sessions_count attended appointments and the Nth was in last 7d
        $sessionsCompleted7d = DB::table('treatments')
            ->when($source, function ($q) use ($source) {
                $q->whereIn('lead_id', function ($qq) use ($source) {
                    $qq->select('id')->from('leads')->where('source', $source);
                });
            })
            ->join('appointments', 'appointments.treatment_id', '=', 'treatments.id')
            ->where('appointments.status', '=', 'attended')
            ->select('treatments.id', DB::raw('COUNT(appointments.id) as attended_count'), DB::raw('MAX(appointments.scheduled_at) as last_attended_at'), 'treatments.sessions_count')
            ->groupBy('treatments.id', 'treatments.sessions_count')
            ->havingRaw('attended_count >= sessions_count')
            ->having('last_attended_at', '>=', $since7d)
            ->count();

        // Daily series (last 7 days, inclusive of today)
        $series = $this->buildDailySeries($since7d, $now, $source);

        return [
            'filters' => [
                'source' => $source,
            ],
            'totals' => [
                'leads_24h' => $leads24h,
                'leads_7d' => $leads7d,
                'appointment_rate_7d' => $appointmentRate7d,
                'attendance_rate_7d' => $attendanceRate7d,
                'conversion_rate_7d' => $conversionRate7d,
                'no_show_rate_7d' => $noShowRate7d,
                'sessions_completed_7d' => $sessionsCompleted7d,
            ],
            'series_7d' => $series,
        ];
    }

    /**
     * Build per-day aggregates for last 7 days.
     *
     * @param Carbon $from
     * @param Carbon $to
     * @param string|null $source
     * @return array<int, array<string, mixed>>
     */
    private function buildDailySeries(Carbon $from, Carbon $to, ?string $source): array
    {
        $days = collect();
        $cursor = $from->copy()->startOfDay();
        $end = $to->copy()->endOfDay();
        while ($cursor->lte($end)) {
            $days->push($cursor->toDateString());
            $cursor->addDay();
        }

        // Leads per day
        $leadsQuery = DB::table('leads')
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->whereBetween('created_at', [$from, $to])
            ->when($source, fn($q) => $q->where('source', $source))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('c', 'd');

        // Appointments created per day
        $apptsCreated = DB::table('appointments')
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->whereBetween('created_at', [$from, $to])
            ->when($source, function ($q) use ($source) {
                $q->whereIn('lead_id', function ($qq) use ($source) {
                    $qq->select('id')->from('leads')->where('source', $source);
                });
            })
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('c', 'd');

        // Appointments by outcome per day (scheduled_at basis)
        $apptOutcomes = DB::table('appointments')
            ->select(DB::raw('DATE(scheduled_at) as d'), 'status', DB::raw('COUNT(*) as c'))
            ->whereBetween('scheduled_at', [$from, $to])
            ->when($source, function ($q) use ($source) {
                $q->whereIn('lead_id', function ($qq) use ($source) {
                    $qq->select('id')->from('leads')->where('source', $source);
                });
            })
            ->whereIn('status', ['attended','no_show','cancelled'])
            ->groupBy(DB::raw('DATE(scheduled_at)'), 'status')
            ->get()
            ->groupBy('d');

        // Treatments created per day (conversion count)
        $treatments = DB::table('treatments')
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->whereBetween('created_at', [$from, $to])
            ->when($source, function ($q) use ($source) {
                $q->whereIn('lead_id', function ($qq) use ($source) {
                    $qq->select('id')->from('leads')->where('source', $source);
                });
            })
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('c', 'd');

        // Sessions completed 6/6 per day (date = day of last attended that reached count)
        $sessionsCompletedRows = DB::table('treatments')
            ->when($source, function ($q) use ($source) {
                $q->whereIn('lead_id', function ($qq) use ($source) {
                    $qq->select('id')->from('leads')->where('source', $source);
                });
            })
            ->join('appointments', 'appointments.treatment_id', '=', 'treatments.id')
            ->where('appointments.status', '=', 'attended')
            ->select('treatments.id as treatment_id', DB::raw('MAX(appointments.scheduled_at) as last_attended_at'), DB::raw('COUNT(appointments.id) as attended_count'), 'treatments.sessions_count')
            ->whereBetween('appointments.scheduled_at', [$from, $to])
            ->groupBy('treatments.id', 'treatments.sessions_count')
            ->havingRaw('attended_count >= sessions_count')
            ->get();

        $sessionsCompleted = collect();
        foreach ($sessionsCompletedRows as $row) {
            $d = Carbon::parse($row->last_attended_at)->toDateString();
            $sessionsCompleted[$d] = ($sessionsCompleted[$d] ?? 0) + 1;
        }

        return $days->map(function (string $d) use ($leadsQuery, $apptsCreated, $apptOutcomes, $treatments, $sessionsCompleted) {
            $outcomes = $apptOutcomes->get($d, collect());
            $attended = $outcomes->where('status', 'attended')->sum('c');
            $noShow = $outcomes->where('status', 'no_show')->sum('c');
            $cancelled = $outcomes->where('status', 'cancelled')->sum('c');
            $den = $attended + $noShow + $cancelled;
            return [
                'date' => $d,
                'leads' => (int) ($leadsQuery[$d] ?? 0),
                'appointments_created' => (int) ($apptsCreated[$d] ?? 0),
                'attended' => (int) $attended,
                'no_show' => (int) $noShow,
                'cancelled' => (int) $cancelled,
                'attendance_rate' => $den > 0 ? $attended / $den : 0.0,
                'no_show_rate' => $den > 0 ? $noShow / $den : 0.0,
                'conversions' => (int) ($treatments[$d] ?? 0),
                'sessions_completed' => (int) ($sessionsCompleted[$d] ?? 0),
            ];
        })->all();
    }

    /**
     * Distinct lead sources available.
     *
     * @return array<int, string>
     */
    public function availableSources(): array
    {
        return Lead::query()
            ->whereNotNull('source')
            ->select('source')
            ->distinct()
            ->orderBy('source')
            ->pluck('source')
            ->all();
    }
}


