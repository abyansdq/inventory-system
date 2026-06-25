@extends('layouts.app')
@section('title', 'Input Barang Masuk')
@section('page-title', 'Input Barang Masuk')

@section('content')
<div class="max-w-3xl" x-data="stockInForm()">

    {{-- Info Pengadaan (jika ada) --}}
    @if($procurements->isNotEmpty())
    <div class="card mb-4 bg-blue-50 border-blue-200">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-800">Ada Pengadaan yang Menunggu Penerimaan</p>
                <p class="text-xs text-blue-600 mt-1">
                    Terdapat {{ $procurements->count() }} pengadaan yang sudah disetujui.
                    Pilih dari dropdown pengadaan di bawah.
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <form action="{{ route('admin.stock-ins.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Pilih dari Pengadaan (opsional) --}}
            @if($procurements->isNotEmpty())
            <div>
                <label class="form-label">Pilih dari Pengadaan (Opsional)</label>
                <select x-model="selectedProcurement"
                        @change="fillFromProcurement($event)"
                        class="form-select">
                    <option value="">-- Input Manual --</option>
                    @foreach($procurements as $proc)
                        <option value="{{ $proc->id }}"
                                data-item="{{ $proc->item_id }}"
                                data-supplier="{{ $proc->supplier_id }}"
                                data-qty="{{ $proc->qty }}"
                                data-harga="{{ $proc->harga_satuan }}">
                            {{ $proc->no_pengadaan }} —
                            {{ $proc->item->nama_barang }} ({{ $proc->qty }} unit)
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="procurement_id" :value="selectedProcurement">
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="form-label">Barang <span class="text-red-500">*</span></label>
                    <select name="item_id" x-model="itemId" class="form-select @error('item_id') border-red-400 @enderror">
                        <option value="">Pilih Barang</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}"
                                    data-stok="{{ $item->stok }}"
                                    data-satuan="{{ $item->satuan }}"
                                    data-supplier="{{ $item->supplier_id }}"
                                    {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_barang }}
                                (Stok: {{ $item->stok }} {{ $item->satuan }})
                            </option>
                        @endforeach
                    </select>
                    @error('item_id') <p class="form-error">{{ $message }}</p> @enderror
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
                    <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', today()->format('Y-m-d')) }}"
                           class="form-input @error('tanggal') border-red-400 @enderror"
                           max="{{ today()->format('Y-m-d') }}">
                    @error('tanggal') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Jumlah (Qty) <span class="text-red-500">*</span></label>
                    <input type="number" name="qty" x-model="qty"
                           value="{{ old('qty') }}"
                           class="form-input @error('qty') border-red-400 @enderror"
                           min="1" @input="hitungTotal">
                    @error('qty') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_satuan" x-model="hargaSatuan"
                           value="{{ old('harga_satuan') }}"
                           class="form-input @error('harga_satuan') border-red-400 @enderror"
                           min="0" step="100" @input="hitungTotal">
                    @error('harga_satuan') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Total Harga (Read Only) --}}
                <div>
                    <label class="form-label">Total Harga</label>
                    <div class="form-input bg-gray-50 font-semibold text-gray-700">
                        Rp <span x-text="formatRupiah(totalHarga)">0</span>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="form-input"
                              placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan Barang Masuk</button>
                <a href="{{ route('admin.stock-ins.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function stockInForm() {
    return {
        itemId: '{{ old('item_id') }}',
        supplierId: '{{ old('supplier_id') }}',
        qty: {{ old('qty', 0) }},
        hargaSatuan: {{ old('harga_satuan', 0) }},
        totalHarga: 0,
        selectedProcurement: '',

        hitungTotal() {
            this.totalHarga = this.qty * this.hargaSatuan;
        },

        fillFromProcurement(event) {
            const opt = event.target.selectedOptions[0];
            if (!opt.value) return;

            this.itemId      = opt.dataset.item;
            this.supplierId  = opt.dataset.supplier;
            this.qty         = parseInt(opt.dataset.qty);
            this.hargaSatuan = parseFloat(opt.dataset.harga);
            this.hitungTotal();
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        }
    }
}
</script>
@endpush