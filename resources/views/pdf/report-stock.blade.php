<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #1f2937; }

        .header { background: #1d4ed8; color: white; padding: 16px 20px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p  { font-size: 9px; opacity: 0.85; margin-top: 3px; }

        .summary { display: flex; gap: 12px; padding: 0 20px; margin-bottom: 16px; }
        .summary-card {
            flex: 1; background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 6px; padding: 10px; text-align: center;
        }
        .summary-card .val { font-size: 18px; font-weight: bold; color: #1d4ed8; }
        .summary-card .lbl { font-size: 8px; color: #6b7280; margin-top: 2px; }

        .content { padding: 0 20px; }

        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        thead tr { background: #1d4ed8; color: white; }
        thead th { padding: 7px 8px; text-align: left; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .badge { padding: 2px 6px; border-radius: 9999px; font-size: 8px; font-weight: 600; }
        .badge-green  { background: #dcfce7; color: #166534; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-red    { background: #fee2e2; color: #991b1b; }

        .footer { margin-top: 20px; padding: 12px 20px; text-align: right;
                  font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ $title }}</h1>
    <p>Tanggal Cetak: {{ $date }} • {{ config('app.name') }}</p>
</div>

<div class="summary">
    <div class="summary-card">
        <div class="val">{{ $items->count() }}</div>
        <div class="lbl">Total Barang</div>
    </div>
    <div class="summary-card">
        <div class="val">Rp {{ number_format($items->sum(fn($i) => $i->stok * $i->harga_beli), 0, ',', '.') }}</div>
        <div class="lbl">Total Nilai Stok</div>
    </div>
    <div class="summary-card">
        <div class="val" style="color:#ca8a04">
            {{ $items->filter(fn($i) => $i->status_stok === 'menipis')->count() }}
        </div>
        <div class="lbl">Stok Menipis</div>
    </div>
    <div class="summary-card">
        <div class="val" style="color:#dc2626">
            {{ $items->filter(fn($i) => $i->stok == 0)->count() }}
        </div>
        <div class="lbl">Stok Habis</div>
    </div>
</div>

<div class="content">
    <table>
        <thead>
            <tr>
                <th style="width:25px">#</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th class="text-center">Stok</th>
                <th class="text-center">Min</th>
                <th class="text-right">Harga Beli</th>
                <th class="text-right">Nilai Stok</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family:monospace">{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->category->nama_kategori }}</td>
                    <td class="text-center">
                        {{ number_format($item->stok) }} {{ $item->satuan }}
                    </td>
                    <td class="text-center">{{ number_format($item->stok_minimum) }}</td>
                    <td class="text-right">
                        Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($item->stok * $item->harga_beli, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @if($item->stok == 0)
                            <span class="badge badge-red">Habis</span>
                        @elseif($item->stok <= $item->stok_minimum)
                            <span class="badge badge-yellow">Menipis</span>
                        @else
                            <span class="badge badge-green">Aman</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    Dicetak oleh: {{ auth()->user()->name }} • {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>