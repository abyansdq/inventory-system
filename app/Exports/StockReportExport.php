<?php
// app/Exports/StockReportExport.php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Http\Request;

class StockReportExport implements
    FromCollection, WithHeadings, WithMapping,
    WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private Request $request) {}

    public function collection()
    {
        $query = Item::with(['category', 'supplier'])->active();

        if ($this->request->filled('category_id')) {
            $query->where('category_id', $this->request->category_id);
        }

        return $query->orderBy('nama_barang')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Supplier',
            'Satuan',
            'Stok',
            'Stok Minimum',
            'Safety Stock',
            'Harga Beli (Rp)',
            'Nilai Stok (Rp)',
            'Status',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $item->kode_barang,
            $item->nama_barang,
            $item->category->nama_kategori,
            $item->supplier->nama_supplier,
            strtoupper($item->satuan),
            $item->stok,
            $item->stok_minimum,
            $item->safety_stock,
            $item->harga_beli,
            $item->stok * $item->harga_beli,
            ucfirst($item->status_stok),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row styling
            1 => [
                'font'    => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'    => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1D4ED8'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Stok';
    }
}