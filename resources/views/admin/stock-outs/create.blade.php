@extends('layouts.app')
@section('title', 'Input Barang Keluar')
@section('page-title', 'Input Barang Keluar')

@section('content')
<div class="max-w-3xl" x-data="stockOutForm()">

    {{-- Info dari Permintaan --}}
    @if($itemRequests->isNotEmpty())
    <div class="card mb-4 bg-blue-50 border-blue-200">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-800">
                    Ada Permintaan yang Sudah Disetujui
                </p>
                <p class="text-xs text-blue-600 mt-1">
                    Terdapat {{ $itemRequests->count() }} permintaan yang menunggu
                    diproses. Pilih dari dropdown di bawah.
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <form action="{{ route('admin.stock-outs.store') }}" method="POST"
              class="space-y-5">
            @csrf

            {{-- Dari Permintaan (opsional) --}}
            @if($itemRequests->isNotEmpty())
            <div>
                <label class="form-label">
                    Dari Permintaan Barang (Opsional)
                </label>
                <select @change="fillFromRequest($event)" class="form-select">
                    <option value="">-- Input Manual --</option>
                    @foreach($itemRequests as $req)
                        <option value="{{ $req->id }}"
                                data-item="{{ $req->item_id }}"
                                data-qty="{{ $req->qty }}">
                            {{ $req->no_permintaan }} —
                            {{ $req->item->nama_barang }}
                            ({{ $req->qty }} {{ $req->item->satuan }})
                            — {{ $req->user->name }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="item_request_id" :value="selectedRequestId">
            </div>
            @endif

            {{-- Barang --}}
            <div>
                <label class="form-label">
                    Barang <span class="text-red-500">*</span>
                </label>
                <select name="item_id" x-model="itemId"
                        class="form-select @error('item_id') border-red-400 @enderror"
                        @change="updateStokInfo($event)">
                    <option value="">Pilih Barang</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}"
                                data-stok="{{ $item->stok }}"
                                data-satuan="{{ $item->satuan }}"
                                {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_barang }}
                            (Stok: {{ $item->stok }} {{ $item->satuan }})
                        </option>
                    @endforeach
                </select>
                @error('item_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info stok tersedia --}}
            <div x-show="stokTersedia !== null" x-transition
                 :class="stokTersedia <= 0
                    ? 'bg-red-50 border-red-200 text-red-800'
                    : 'bg-green-50 border-green-200 text-green-800'"
                 class="p-3 rounded-xl border text-sm">
                <span x-show="stokTersedia > 0">
                    ✅ Stok tersedia:
                    <strong x-text="stokTersedia + ' ' + satuan"></strong>
                </span>
                <span x-show="stokTersedia <= 0">
                    ⛔ Stok barang ini habis!
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">
                        Jumlah (Qty) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="qty" x-model="qty"
                               value="{{ old('qty') }}"
                               class="form-input pr-16
                                      @error('qty') border-red-400 @enderror"
                               min="1" :max="stokTersedia">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2
                                     text-gray-400 text-sm"
                              x-text="satuan">unit</span>
                    </div>
                    @error('qty')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal"
                           value="{{ old('tanggal', today()->format('Y-m-d')) }}"
                           class="form-input @error('tanggal') border-red-400 @enderror"
                           max="{{ today()->format('Y-m-d') }}">
                    @error('tanggal')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="form-input"
                              placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary"
                        :disabled="stokTersedia !== null && stokTersedia <= 0">
                    Simpan Barang Keluar
                </button>
                <a href="{{ route('admin.stock-outs.index') }}" class="btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function stockOutForm() {
    return {
        itemId: '{{ old('item_id') }}',
        qty: {{ old('qty', 1) }},
        stokTersedia: null,
        satuan: 'unit',
        selectedRequestId: '',

        updateStokInfo(event) {
            const opt = event.target.selectedOptions[0];
            if (opt && opt.value) {
                this.stokTersedia = parseInt(opt.dataset.stok);
                this.satuan       = opt.dataset.satuan;
            } else {
                this.stokTersedia = null;
            }
        },

        fillFromRequest(event) {
            const opt = event.target.selectedOptions[0];
            if (!opt.value) {
                this.selectedRequestId = '';
                return;
            }

            this.selectedRequestId = opt.value;
            this.itemId            = opt.dataset.item;
            this.qty               = parseInt(opt.dataset.qty);

            // Update select barang
            const itemSelect = document.querySelector('select[name="item_id"]');
            if (itemSelect) {
                itemSelect.value = this.itemId;
                const selectedOpt = itemSelect.selectedOptions[0];
                if (selectedOpt) {
                    this.stokTersedia = parseInt(selectedOpt.dataset.stok);
                    this.satuan       = selectedOpt.dataset.satuan;
                }
            }
        }
    }
}
</script>
@endpush