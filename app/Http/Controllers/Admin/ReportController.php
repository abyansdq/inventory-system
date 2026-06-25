<?php
// app/Http/Controllers/Admin/ReportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Procurement;
use App\Models\Forecast;
use App\Models\EoqCalculation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockReportExport;
use App\Exports\StockInReportExport;
use App\Exports\StockOutReportExport;
use App\Exports\ProcurementReportExport;

class ReportController extends Controller
{
    // -------------------------------------------------------
    // Laporan Stok
    // -------------------------------------------------------
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
                'aman'    => $query->where('stok', '>', 0)->whereColumn('stok', '>', 'stok_minimum'),
                default   => null,
            };
        }

        $items      = $query->orderBy('nama_barang')->get();
        $categories = Category::active()->get();
        $summary    = [
            'total_item'    => $items->count(),
            'nilai_total'   => $items->sum(fn($i) => $i->stok * $i->harga_beli),
            'stok_menipis'  => $items->filter(fn($i) => $i->status_stok === 'menipis')->count(),
            'stok_habis'    => $items->filter(fn($i) => $i->stok == 0)->count(),
        ];

        return view('admin.reports.stock', compact(
            'items', 'categories', 'summary'
        ));
    }

    // -------------------------------------------------------
    // Laporan Barang Masuk
    // -------------------------------------------------------
    public function stockIn(Request $request)
    {
        $query = StockIn::with(['item', 'supplier', 'user']);

        $this->applyDateFilter($query, $request);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $stockIns = $query->latest('tanggal')->get();
        $items    = Item::active()->get();
        $summary  = [
            'total_transaksi' => $stockIns->count(),
            'total_qty'       => $stockIns->sum('qty'),
            'total_nilai'     => $stockIns->sum('total_harga'),
        ];

        return view('admin.reports.stock-in', compact(
            'stockIns', 'items', 'summary'
        ));
    }

    // -------------------------------------------------------
    // Laporan Barang Keluar
    // -------------------------------------------------------
    public function stockOut(Request $request)
    {
        $query = StockOut::with(['item', 'user', 'itemRequest.user']);

        $this->applyDateFilter($query, $request);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $stockOuts = $query->latest('tanggal')->get();
        $items     = Item::active()->get();
        $summary   = [
            'total_transaksi' => $stockOuts->count(),
            'total_qty'       => $stockOuts->sum('qty'),
        ];

        return view('admin.reports.stock-out', compact(
            'stockOuts', 'items', 'summary'
        ));
    }

    // -------------------------------------------------------
    // Laporan Pengadaan
    // -------------------------------------------------------
    public function procurement(Request $request)
    {
        $query = Procurement::with(['item', 'supplier', 'user', 'approvedBy']);

        $this->applyDateFilter($query, $request);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $procurements = $query->latest()->get();
        $summary      = [
            'total'          => $procurements->count(),
            'total_nilai'    => $procurements->sum('total_harga'),
            'approved'       => $procurements->where('status', 'approved')->count(),
            'received'       => $procurements->where('status', 'received')->count(),
        ];

        return view('admin.reports.procurement', compact(
            'procurements', 'summary'
        ));
    }

    // -------------------------------------------------------
    // Laporan Prediksi (Forecast)
    // -------------------------------------------------------
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

        return view('admin.reports.forecast', compact(
            'forecasts', 'items'
        ));
    }

    // -------------------------------------------------------
    // Export PDF
    // -------------------------------------------------------
    public function exportPdf(Request $request, string $type)
    {
        $data = match($type) {
            'stock' => [
                'items'  => Item::with(['category', 'supplier'])->active()->get(),
                'title'  => 'Laporan Stok Barang',
                'date'   => now()->format('d F Y'),
            ],
            'stock-in' => [
                'items'  => StockIn::with(['item', 'supplier'])->get(),
                'title'  => 'Laporan Barang Masuk',
                'date'   => now()->format('d F Y'),
                'tanggal_dari'   => $request->tanggal_dari,
                'tanggal_sampai' => $request->tanggal_sampai,
            ],
            'stock-out' => [
                'items'  => StockOut::with(['item', 'user'])->get(),
                'title'  => 'Laporan Barang Keluar',
                'date'   => now()->format('d F Y'),
            ],
            'procurement' => [
                'items'  => Procurement::with(['item', 'supplier'])->get(),
                'title'  => 'Laporan Pengadaan',
                'date'   => now()->format('d F Y'),
            ],
            default => abort(404),
        };

        $pdf = Pdf::loadView("pdf.report-{$type}", $data)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
            ]);

        return $pdf->download("laporan-{$type}-" . now()->format('Ymd') . ".pdf");
    }

    // -------------------------------------------------------
    // Export Excel
    // -------------------------------------------------------
    public function exportExcel(Request $request, string $type)
    {
        $filename = "laporan-{$type}-" . now()->format('Ymd') . ".xlsx";

        return match($type) {
            'stock'       => Excel::download(new StockReportExport($request), $filename),
            'stock-in'    => Excel::download(new StockInReportExport($request), $filename),
            'stock-out'   => Excel::download(new StockOutReportExport($request), $filename),
            'procurement' => Excel::download(new ProcurementReportExport($request), $filename),
            default       => abort(404),
        };
    }

    // -------------------------------------------------------
    // Helper
    // -------------------------------------------------------
    private function applyDateFilter($query, Request $request): void
    {
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
    }
}