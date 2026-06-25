@extends('layouts.app')
@section('title', 'Buat Pengadaan')
@section('page-title', 'Buat Pengadaan Barang')

@section('content')
<div class="max-w-3xl" x-data="procurementForm()">
    <div class="card">
        <form action="{{ route('manajer.procurements.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="form-label">Barang <span class="text-red-500">*</span></label>
                    <select name="item_id" class="form-select @error('item_id') border-red-400 @enderror"
                            @change="selectItem($event)">
                        <option value="">Pilih Barang</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}"
                                    data-stok="{{ $item->stok }}"
                                    data-satuan="{{ $item->satuan }}"
                                    data-supplier="{{ $item->supplier_id }}"
                                    data-eoq="{{ $item->eoqCalculations->first()?->eoq_result ?? 0 }}"
                                    {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_barang }}
                                (Stok: {{ $item->stok }} {{ $item->satuan }})
                            </option>
                        @endforeach
                    </select>
                    @error('item_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Rekomendasi EOQ --}}
                <div class="sm:col-span-2" x-show="eoqRekomendasi > 0" x-transition>
                    <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm">
                        <p class="font-medium text-green-800">
                            💡 Rekomendasi EOQ:
                            <strong x-text="eoqRekomendasi + ' ' + satuan"></strong>
                        </p>
                        <p class="text-xs text-green-600 mt-1">
                            Jumlah pemesanan optimal berdasarkan kalkulasi EOQ terakhir.
                            <a href="#" @click.prevent="qty = eoqRekomendasi; hitungTotal()"
                               class="underline">Gunakan nilai ini</a>
                        </p>
                    </div>
                </div>

                <div>
                    <label class="form-label">Supplier <span class="text-red-500">*</span></label>
                    <select name="supplier_id" x-model="supplierId"
                            class="form-select @error('supplier_id') border-red-400 @enderror">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Tanggal Pengadaan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal"
                           value="{{ old('tanggal', today()->format('Y-m-d')) }}"
                           class="form-input @error('tanggal') border-red-400 @enderror">
                    @error('tanggal') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Jumlah (Qty) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="qty" x-model="qty"
                               class="form-input pr-16 @error('qty') border-red-400 @enderror"
                               min="1" @input="hitungTotal">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                              x-text="satuan">unit</span>
                    </div>
                    @error('qty') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_satuan" x-model="hargaSatuan"
                           class="form-input @error('harga_satuan') border-red-400 @enderror"
                           min="0" step="100" @input="hitungTotal">
                    @error('harga_satuan') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Total Harga</label>
                    <div class="form-input bg-gray-50 font-semibold text-gray-700">
                        Rp <span x-text="formatRupiah(totalHarga)">0</span>
                    </div>
                </div>

                <div>
                    <label class="form-label">Tanggal Dibutuhkan</label>
                    <input type="date" name="tanggal_dibutuhkan"
                           value="{{ old('tanggal_dibutuhkan') }}"
                           class="form-input">
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" rows="2" class="form-input"
                              placeholder="Catatan pengadaan (opsional)">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Ajukan Pengadaan</button>
                <a href="{{ route('manajer.procurements.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function procurementForm() {
    return {
        qty: {{ old('qty', 0) }},
        hargaSatuan: {{ old('harga_satuan', 0) }},
        totalHarga: 0,
        supplierId: '{{ old('supplier_id') }}',
        satuan: 'unit',
        eoqRekomendasi: 0,

        selectItem(event) {
            const opt = event.target.selectedOptions[0];
            if (!opt.value) return;
            this.satuan         = opt.dataset.satuan;
            this.supplierId     = opt.dataset.supplier;
            this.eoqRekomendasi = parseFloat(opt.dataset.eoq) || 0;
        },

        hitungTotal() {
            this.totalHarga = this.qty * this.hargaSatuan;
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        }
    }
}
</script>
@endpush