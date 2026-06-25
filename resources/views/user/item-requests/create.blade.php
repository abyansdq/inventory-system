@extends('layouts.app')
@section('title', 'Buat Permintaan')
@section('page-title', 'Buat Permintaan Barang')

@section('content')
<div class="max-w-2xl" x-data="requestForm()" x-init="init()">
    <div class="card">
        <form action="{{ route('user.item-requests.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="form-label">Pilih Barang <span class="text-red-500">*</span></label>
                <select name="item_id" class="form-select @error('item_id') border-red-400 @enderror"
                        @change="selectItem($event)">
                    <option value="">Pilih Barang</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}"
                                data-stok="{{ $item->stok }}"
                                data-satuan="{{ $item->satuan }}"
                                {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_barang }} — Stok: {{ $item->stok }} {{ $item->satuan }}
                        </option>
                    @endforeach
                </select>
                @error('item_id') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Info stok (realtime) --}}
            <div x-show="stokInfo" x-transition
                 class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm">
                <p>Stok tersedia: <strong x-text="stokInfo + ' ' + satuan"></strong></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Jumlah <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="qty" value="{{ old('qty', 1) }}"
                               class="form-input pr-16 @error('qty') border-red-400 @enderror"
                               min="1" :max="stokInfo">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                              x-text="satuan">unit</span>
                    </div>
                    @error('qty') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Dibutuhkan Tanggal</label>
                    <input type="date" name="tanggal_butuh"
                           value="{{ old('tanggal_butuh') }}"
                           class="form-input"
                           min="{{ today()->format('Y-m-d') }}">
                    @error('tanggal_butuh') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="form-label">Keperluan <span class="text-red-500">*</span></label>
                <textarea name="keperluan" rows="3"
                          class="form-input @error('keperluan') border-red-400 @enderror"
                          placeholder="Jelaskan keperluan penggunaan barang...">{{ old('keperluan') }}</textarea>
                @error('keperluan') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Ajukan Permintaan</button>
                <a href="{{ route('user.item-requests.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function requestForm() {
    return {
        stokInfo: null,
        satuan: 'unit',

        init() {
            // Auto-select item jika ada query param
            const urlParams = new URLSearchParams(window.location.search);
            const itemId    = urlParams.get('item_id');

            if (itemId) {
                const select = document.querySelector('select[name="item_id"]');
                if (select) {
                    select.value = itemId;
                    // Trigger perubahan
                    const opt = select.selectedOptions[0];
                    if (opt) {
                        this.stokInfo = opt.dataset.stok;
                        this.satuan   = opt.dataset.satuan;
                    }
                }
            }
        },

        selectItem(event) {
            const opt = event.target.selectedOptions[0];
            if (opt && opt.value) {
                this.stokInfo = opt.dataset.stok;
                this.satuan   = opt.dataset.satuan;
            } else {
                this.stokInfo = null;
            }
        }
    }
}
</script>
@endpush