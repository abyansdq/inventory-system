<?php
// app/Exports/StockInReportExport.php

namespace App\Exports;

use App\Models\StockIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Http\Request;

class StockInReportExport implements
    FromCollection, WithHeadings, WithMapping,
    WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private Request $request) {}

    public function collection()
    {
        $query = StockIn::with(['item', 'supplier', 'user']);

        if ($this->request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $this->request->tanggal_dari);
        }
        if ($this->request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $this->request->tanggal_sampai);
        }

        return $query->latest('tanggal')->get();
    }

    public function headings(): array
    {
        return [
            'No', 'No Dokumen', 'Tanggal', 'Barang',
            'Supplier', 'Qty', 'Satuan', 'Harga Satuan (Rp)',
            'Total (Rp)', 'Input Oleh', 'Keterangan',
        ];
    }

    public function map($si): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $si->no_dokumen,
            $si->tanggal->format('d/m/Y'),
            $si->item->nama_barang,
            $si->supplier->nama_supplier,
            $si->qty,
            strtoupper($si->item->satuan),
            $si->harga_satuan,
            $si->total_harga,
            $si->user->name,
            $si->keterangan ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF16A34A'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Barang Masuk';
    }
}