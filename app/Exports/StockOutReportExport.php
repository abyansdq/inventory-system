<?php
// app/Exports/StockOutReportExport.php

namespace App\Exports;

use App\Models\StockOut;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Http\Request;

class StockOutReportExport implements
    FromCollection, WithHeadings, WithMapping,
    WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private Request $request) {}

    public function collection()
    {
        $query = StockOut::with(['item', 'user', 'itemRequest']);

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
            'Qty', 'Satuan', 'No Permintaan', 'Input Oleh', 'Keterangan',
        ];
    }

    public function map($so): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $so->no_dokumen,
            $so->tanggal->format('d/m/Y'),
            $so->item->nama_barang,
            $so->qty,
            strtoupper($so->item->satuan),
            $so->itemRequest?->no_permintaan ?? '-',
            $so->user->name,
            $so->keterangan ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFDC2626'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Barang Keluar';
    }
}