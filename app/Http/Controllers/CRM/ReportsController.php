<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Domain\Services\ReportsService;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request, ReportsService $reports)
    {
        $source = $request->query('source');
        $data = $reports->buildKpis($source);
        $sources = $reports->availableSources();

        return view('crm.reports.index', compact('data', 'sources'));
    }

    public function apiKpis(Request $request, ReportsService $reports)
    {
        $validated = $request->validate([
            'source' => ['nullable','string']
        ]);
        return response()->json($reports->buildKpis($validated['source'] ?? null));
    }
}




