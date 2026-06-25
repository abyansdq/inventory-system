<?php
// app/Http/Controllers/Admin/DemandHistoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandHistory;
use App\Models\Item;
use Illuminate\Http\Request;

class DemandHistoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_id'           => ['required', 'exists:items,id'],
            'tahun'             => ['required', 'integer', 'min:2000', 'max:' . date('Y')],
            'bulan'             => ['required', 'integer', 'between:1,12'],
            'jumlah_permintaan' => ['required', 'integer', 'min:0'],
        ]);

        DemandHistory::updateOrCreate(
            [
                'item_id' => $request->item_id,
                'tahun'   => $request->tahun,
                'bulan'   => $request->bulan,
            ],
            ['jumlah_permintaan' => $request->jumlah_permintaan]
        );

        $item = Item::find($request->item_id);

        return redirect()
            ->route('admin.eoq.show', $item)
            ->with('success', 'Histori permintaan berhasil disimpan.');
    }

    public function destroy(DemandHistory $demandHistory)
    {
        $itemId = $demandHistory->item_id;
        $demandHistory->delete();

        return redirect()
            ->route('admin.eoq.show', $itemId)
            ->with('success', 'Histori berhasil dihapus.');
    }
}