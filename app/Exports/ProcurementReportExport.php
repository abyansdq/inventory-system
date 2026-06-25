<?php
// app/Exports/ProcurementReportExport.php

namespace App\Exports;

use App\Models\Procurement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Http\Request;

class ProcurementReportExport implements
    FromCollection, WithHeadings, WithMapping,
    WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private Request $request) {}

    public function collection()
    {
        $query = Procurement::with(['item', 'supplier', 'user', 'approvedBy']);

        if ($this->request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $this->request->tanggal_dari);
        }
        if ($this->request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $this->request->tanggal_sampai);
        }
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No', 'No Pengadaan', 'Tanggal', 'Barang', 'Supplier',
            'Qty', 'Satuan', 'Harga Satuan (Rp)', 'Total (Rp)',
            'Status', 'Diajukan Oleh', 'Disetujui Oleh', 'Catatan',
        ];
    }

    public function map($proc): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $proc->no_pengadaan,
            $proc->tanggal->format('d/m/Y'),
            $proc->item->nama_barang,
            $proc->supplier->nama_supplier,
            $proc->qty,
            strtoupper($proc->item->satuan),
            $proc->harga_satuan,
            $proc->total_harga,
            $proc->status_label,
            $proc->user->name,
            $proc->approvedBy?->name ?? '-',
            $proc->catatan ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF7C3AED'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Pengadaan';
    }
}