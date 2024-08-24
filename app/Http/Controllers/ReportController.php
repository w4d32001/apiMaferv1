<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

use Carbon\Carbon;
class ReportController extends Controller
{
    public function reporteVentas(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $sales = Sale::where('created_at', '>=', $startDate)
             ->where('created_at', '<=', $endDate)
             ->get();

        $pdf = PDF::loadView('reports.sales', compact('sales'));
        return $pdf->download('reporte_ventass.pdf');
    }
}
