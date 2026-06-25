<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
         ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified.active'])->group(function () {

     Route::patch('profile/password',[\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    
     // Dashboard — redirect sesuai role
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');

    /*
    |------------------------------------------------------------------
    | ADMIN ROUTES
    |------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'admin'])
             ->name('dashboard');

        // Master Data
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('suppliers',  \App\Http\Controllers\Admin\SupplierController::class);
        Route::resource('items',      \App\Http\Controllers\Admin\ItemController::class);
        Route::resource('users',      \App\Http\Controllers\Admin\UserController::class);

        // Transaksi
        Route::resource('stock-ins',  \App\Http\Controllers\Admin\StockInController::class);
        Route::resource('stock-outs', \App\Http\Controllers\Admin\StockOutController::class);

        // Item Requests — Admin kelola (approve/reject)
        Route::get('item-requests',          [\App\Http\Controllers\Admin\ItemRequestController::class, 'index'])->name('item-requests.index');
        Route::get('item-requests/{itemRequest}', [\App\Http\Controllers\Admin\ItemRequestController::class, 'show'])->name('item-requests.show');
        Route::patch('item-requests/{itemRequest}/approve', [\App\Http\Controllers\Admin\ItemRequestController::class, 'approve'])->name('item-requests.approve');
        Route::patch('item-requests/{itemRequest}/reject',  [\App\Http\Controllers\Admin\ItemRequestController::class, 'reject'])->name('item-requests.reject');
        Route::patch('item-requests/{itemRequest}/process', [\App\Http\Controllers\Admin\ItemRequestController::class, 'process'])->name('item-requests.process');

        // EOQ & Analisis
        Route::get('eoq',                          [\App\Http\Controllers\Admin\EoqController::class, 'index'])->name('eoq.index');
        Route::get('eoq/{item}',                   [\App\Http\Controllers\Admin\EoqController::class, 'show'])->name('eoq.show');
        Route::post('eoq/{item}/calculate',        [\App\Http\Controllers\Admin\EoqController::class, 'calculate'])->name('eoq.calculate');

        // Forecast
        Route::get('forecasts',                    [\App\Http\Controllers\Admin\ForecastController::class, 'index'])->name('forecasts.index');
        Route::get('forecasts/{item}',             [\App\Http\Controllers\Admin\ForecastController::class, 'show'])->name('forecasts.show');
        Route::post('forecasts/{item}/generate',   [\App\Http\Controllers\Admin\ForecastController::class, 'generate'])->name('forecasts.generate');

        // Monitoring
        Route::get('monitoring', [\App\Http\Controllers\Admin\MonitoringController::class, 'index'])->name('monitoring.index');

        // Laporan
        Route::get('reports/stock',        [\App\Http\Controllers\Admin\ReportController::class, 'stock'])->name('reports.stock');
        Route::get('reports/stock-in',     [\App\Http\Controllers\Admin\ReportController::class, 'stockIn'])->name('reports.stock-in');
        Route::get('reports/stock-out',    [\App\Http\Controllers\Admin\ReportController::class, 'stockOut'])->name('reports.stock-out');
        Route::get('reports/procurement',  [\App\Http\Controllers\Admin\ReportController::class, 'procurement'])->name('reports.procurement');
        Route::get('reports/forecast',     [\App\Http\Controllers\Admin\ReportController::class, 'forecast'])->name('reports.forecast');

        // Export PDF & Excel
        Route::get('reports/export/pdf/{type}',   [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('reports/export/excel/{type}', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('reports.export.excel');

        // Procurement — Admin hanya lihat
        Route::get('procurements', [\App\Http\Controllers\Admin\ProcurementController::class, 'index'])->name('procurements.index');
        Route::get('procurements/{procurement}', [\App\Http\Controllers\Admin\ProcurementController::class, 'show'])->name('procurements.show');

        // Demand History
     Route::post('demand-histories',[\App\Http\Controllers\Admin\DemandHistoryController::class, 'store'])->name('demand-histories.store');
     Route::delete('demand-histories/{demandHistory}',     [\App\Http\Controllers\Admin\DemandHistoryController::class, 'destroy'])->name('demand-histories.destroy');
     
     Route::patch('users/{user}/toggle-active',[\App\Http\Controllers\Admin\UserController::class, 'toggleActive']) ->name('users.toggle-active');
     
     Route::get('activity-logs',    [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])    ->name('activity-logs.index');

     });

    /*
    |------------------------------------------------------------------
    | MANAJER ROUTES
    |------------------------------------------------------------------
    */
    Route::middleware(['role:manajer'])->prefix('manajer')->name('manajer.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'manajer'])
             ->name('dashboard');

        // Lihat stok & barang
        Route::get('items',           [\App\Http\Controllers\Manajer\ItemController::class, 'index'])->name('items.index');
        Route::get('items/{item}',    [\App\Http\Controllers\Manajer\ItemController::class, 'show'])->name('items.show');

        // Procurement — Manajer kelola
        Route::resource('procurements', \App\Http\Controllers\Manajer\ProcurementController::class);
        Route::patch('procurements/{procurement}/approve', [\App\Http\Controllers\Manajer\ProcurementController::class, 'approve'])->name('procurements.approve');
        Route::patch('procurements/{procurement}/reject',  [\App\Http\Controllers\Manajer\ProcurementController::class, 'reject'])->name('procurements.reject');
        Route::patch('procurements/{procurement}/submit',  [\App\Http\Controllers\Manajer\ProcurementController::class, 'submit'])->name('procurements.submit');

        // EOQ & Forecast — hanya lihat
        Route::get('eoq',               [\App\Http\Controllers\Manajer\EoqController::class, 'index'])->name('eoq.index');
        Route::get('eoq/{item}',        [\App\Http\Controllers\Manajer\EoqController::class, 'show'])->name('eoq.show');
        Route::get('forecasts',         [\App\Http\Controllers\Manajer\ForecastController::class, 'index'])->name('forecasts.index');
        Route::get('forecasts/{item}',  [\App\Http\Controllers\Manajer\ForecastController::class, 'show'])->name('forecasts.show');

        // Monitoring
        Route::get('monitoring', [\App\Http\Controllers\Manajer\MonitoringController::class, 'index'])->name('monitoring.index');

        // Laporan
        Route::get('reports/stock',       [\App\Http\Controllers\Manajer\ReportController::class, 'stock'])->name('reports.stock');
        Route::get('reports/procurement', [\App\Http\Controllers\Manajer\ReportController::class, 'procurement'])->name('reports.procurement');
        Route::get('reports/forecast',    [\App\Http\Controllers\Manajer\ReportController::class, 'forecast'])->name('reports.forecast');
        Route::get('reports/export/pdf/{type}',   [\App\Http\Controllers\Manajer\ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('reports/export/excel/{type}', [\App\Http\Controllers\Manajer\ReportController::class, 'exportExcel'])->name('reports.export.excel');

        // Item Requests — hanya lihat
        Route::get('item-requests',               [\App\Http\Controllers\Manajer\ItemRequestController::class, 'index'])->name('item-requests.index');
        Route::get('item-requests/{itemRequest}', [\App\Http\Controllers\Manajer\ItemRequestController::class, 'show'])->name('item-requests.show');
    });

    /*
    |------------------------------------------------------------------
    | USER ROUTES
    |------------------------------------------------------------------
    */
    Route::middleware(['role:user'])->prefix('user')->name('user.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'user'])
             ->name('dashboard');

        // Lihat stok
        Route::get('items',        [\App\Http\Controllers\User\ItemController::class, 'index'])->name('items.index');
        Route::get('items/{item}', [\App\Http\Controllers\User\ItemController::class, 'show'])->name('items.show');

        // Permintaan barang
        Route::get('item-requests',
        [\App\Http\Controllers\User\ItemRequestController::class, 'index'])
        ->name('item-requests.index');
          Route::get('item-requests/create',
               [\App\Http\Controllers\User\ItemRequestController::class, 'create'])
               ->name('item-requests.create');
          Route::post('item-requests',
               [\App\Http\Controllers\User\ItemRequestController::class, 'store'])
               ->name('item-requests.store');
          Route::get('item-requests/{itemRequest}',
               [\App\Http\Controllers\User\ItemRequestController::class, 'show'])
               ->name('item-requests.show');
          Route::patch('item-requests/{itemRequest}/cancel',
               [\App\Http\Controllers\User\ItemRequestController::class, 'cancel'])
               ->name('item-requests.cancel');

        // Forecast — hanya lihat
        Route::get('forecasts',        [\App\Http\Controllers\User\ForecastController::class, 'index'])->name('forecasts.index');
        Route::get('forecasts/{item}', [\App\Http\Controllers\User\ForecastController::class, 'show'])->name('forecasts.show');
    });

    /*
    |------------------------------------------------------------------
    | SHARED ROUTES (semua role bisa akses)
    |------------------------------------------------------------------
    */
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifikasi
    Route::get('notifications',              [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{id}/read',  [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('notifications/read-all',   [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});