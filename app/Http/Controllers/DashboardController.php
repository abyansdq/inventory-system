<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\ItemRequest;
use App\Models\Procurement;

class DashboardController extends Controller
{
    /**
     * Entry point — redirect ke dashboard sesuai role.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('manajer')) {
            return redirect()->route('manajer.dashboard');
        }

        if ($user->hasRole('user')) {
            return redirect()->route('user.dashboard');
        }

        abort(403, 'Role tidak dikenali.');
    }

    /**
     * Dashboard Admin Gudang.
     */
    public function admin(): View
    {
        $today = today();

        $data = [
            // KPI Cards
            'total_barang'          => Item::active()->count(),
            'total_supplier'        => Supplier::active()->count(),
            'total_kategori'        => Category::active()->count(),
            'barang_masuk_hari_ini' => StockIn::whereDate('tanggal', $today)->sum('qty'),
            'barang_keluar_hari_ini'=> StockOut::whereDate('tanggal', $today)->sum('qty'),
            'stok_menipis'          => Item::lowStock()->count(),
            'pengajuan_pending'     => ItemRequest::pending()->count(),
            'procurement_pending'   => Procurement::pending()->count(),

            // Data untuk tabel
            'items_low_stock'       => Item::with(['category', 'supplier'])
                                            ->lowStock()
                                            ->orderBy('stok')
                                            ->take(10)
                                            ->get(),

            // Pergerakan stok 7 hari terakhir (untuk grafik)
            'stock_in_chart'        => $this->getStockMovementChart('in', 7),
            'stock_out_chart'       => $this->getStockMovementChart('out', 7),

            // Pengajuan terbaru
            'latest_requests'       => ItemRequest::with(['user', 'item'])
                                            ->latest()
                                            ->take(5)
                                            ->get(),
        ];

        return view('admin.dashboard', $data);
    }

    /**
     * Dashboard Manajer.
     */
    public function manajer(): View
    {
        $data = [
            'total_barang'          => Item::active()->count(),
            'stok_menipis'          => Item::lowStock()->count(),
            'procurement_pending'   => Procurement::pending()->count(),
            'procurement_approved'  => Procurement::approved()->count(),

            // Item perlu reorder
            'items_need_reorder'    => Item::with(['category', 'supplier'])
                                            ->lowStock()
                                            ->take(10)
                                            ->get(),

            // Procurement terbaru
            'latest_procurements'   => Procurement::with(['item', 'supplier', 'user'])
                                            ->latest()
                                            ->take(5)
                                            ->get(),

            // EOQ terbaru per item
            'eoq_summary'           => Item::with(['eoqCalculations' => function ($q) {
                                            $q->latest()->limit(1);
                                        }])
                                            ->active()
                                            ->take(5)
                                            ->get(),
        ];

        return view('manajer.dashboard', $data);
    }

    /**
     * Dashboard User.
     */
    public function user(): View
    {
        $userId = Auth::id();

        $data = [
            'total_permintaan'      => ItemRequest::where('user_id', $userId)->count(),
            'permintaan_pending'    => ItemRequest::where('user_id', $userId)->pending()->count(),
            'permintaan_approved'   => ItemRequest::where('user_id', $userId)->approved()->count(),

            // Permintaan terbaru milik user ini
            'latest_requests'       => ItemRequest::with(['item'])
                                            ->where('user_id', $userId)
                                            ->latest()
                                            ->take(5)
                                            ->get(),

            // Barang tersedia
            'items_available'       => Item::active()
                                            ->where('stok', '>', 0)
                                            ->with('category')
                                            ->take(10)
                                            ->get(),
        ];

        return view('user.dashboard', $data);
    }

    /**
     * Helper: Data grafik pergerakan stok N hari terakhir.
     */
    private function getStockMovementChart(string $type, int $days): array
    {
        $model  = $type === 'in' ? StockIn::class : StockOut::class;
        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date     = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('d/m');
            $values[] = $model::whereDate('tanggal', $date)->sum('qty') ?? 0;
        }

        return ['labels' => $labels, 'values' => $values];
    }
}