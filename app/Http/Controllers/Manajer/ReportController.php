<?php
// app/Http/Controllers/Manajer/ReportController.php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Procurement;
use App\Models\Forecast;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockReportExport;
use App\Exports\ProcurementReportExport;

class ReportController extends Controller
{
    public function stock(Request $request)
    {
        $query = Item::with(['category', 'supplier'])->active();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status_stok')) {
            match($request->status_stok) {
                'menipis' => $query->lowStock(),
                'habis'   => $query->where('stok', 0),
                'aman'    => $query->where('stok', '>', 0)
                                   ->whereColumn('stok', '>', 'stok_minimum'),
                default   => null,
            };
        }

        $items   = $query->orderBy('nama_barang')->get();
        $summary = [
            'total_item'   => $items->count(),
            'nilai_total'  => $items->sum(fn($i) => $i->stok * $i->harga_beli),
            'stok_menipis' => $items->filter(
                fn($i) => $i->status_stok === 'menipis'
            )->count(),
            'stok_habis'   => $items->filter(
                fn($i) => $i->stok == 0
            )->count(),
        ];

        $categories = \App\Models\Category::active()->get();

        return view('manajer.reports.stock', compact('items', 'summary', 'categories'));
    }

    public function procurement(Request $request)
    {
        $query = Procurement::with(['item', 'supplier', 'user', 'approvedBy']);

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $procurements = $query->latest()->get();
        $summary = [
            'total'       => $procurements->count(),
            'total_nilai' => $procurements->sum('total_harga'),
            'approved'    => $procurements->where('status', 'approved')->count(),
            'received'    => $procurements->where('status', 'received')->count(),
        ];

        return view('manajer.reports.procurement', compact('procurements', 'summary'));
    }

    public function forecast(Request $request)
    {
        $query = Forecast::with(['item', 'generatedBy'])
            ->where('metode', 'weighted_moving_average');

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_prediksi', $request->tahun);
        }

        $forecasts = $query->orderBy('tahun_prediksi')
                           ->orderBy('bulan_prediksi')
                           ->get();

        $items = Item::active()->get();

        return view('manajer.reports.forecast', compact('forecasts', 'items'));
    }

    public function exportPdf(Request $request, string $type)
    {
        $data = match($type) {
            'stock' => [
                'items' => Item::with(['category', 'supplier'])->active()->get(),
                'title' => 'Laporan Stok Barang',
                'date'  => now()->format('d F Y'),
            ],
            'procurement' => [
                'items' => Procurement::with(['item', 'supplier'])->get(),
                'title' => 'Laporan Pengadaan',
                'date'  => now()->format('d F Y'),
            ],
            default => abort(404),
        };

        $pdf = Pdf::loadView("pdf.report-{$type}", $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download("laporan-{$type}-" . now()->format('Ymd') . ".pdf");
    }

    public function exportExcel(Request $request, string $type)
    {
        $filename = "laporan-{$type}-" . now()->format('Ymd') . ".xlsx";

        return match($type) {
            'stock'       => Excel::download(new StockReportExport($request), $filename),
            'procurement' => Excel::download(new ProcurementReportExport($request), $filename),
            default       => abort(404),
        };
    }
}