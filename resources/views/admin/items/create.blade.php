@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('page-title', 'Tambah Barang')

@section('content')
<div class="max-w-4xl">
    <div class="card">
        <form action="{{ route('admin.items.store') }}" method="POST"
              enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Informasi Dasar --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Informasi Dasar</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Kode Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_barang"
                               value="{{ old('kode_barang', $kodeDefault) }}"
                               class="form-input @error('kode_barang') border-red-400 @enderror">
                        @error('kode_barang') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                        <select name="satuan" class="form-select @error('satuan') border-red-400 @enderror">
                            <option value="">Pilih Satuan</option>
                            @foreach(['pcs','kg','gram','liter','ml','meter','cm','box','roll','unit','lembar','botol','karung'] as $sat)
                                <option value="{{ $sat }}" {{ old('satuan') === $sat ? 'selected' : '' }}>
                                    {{ strtoupper($sat) }}
                                </option>
                            @endforeach
                        </select>
                        @error('satuan') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang') }}"
                               class="form-input @error('nama_barang') border-red-400 @enderror"
                               placeholder="Nama barang lengkap">
                        @error('nama_barang') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') border-red-400 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Supplier <span class="text-red-500">*</span></label>
                        <select name="supplier_id" class="form-select @error('supplier_id') border-red-400 @enderror">
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Stok --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Informasi Stok</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Stok Awal <span class="text-red-500">*</span></label>
                        <input type="number" name="stok" value="{{ old('stok', 0) }}"
                               class="form-input" min="0">
                        @error('stok') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Stok Minimum <span class="text-red-500">*</span></label>
                        <input type="number" name="stok_minimum" value="{{ old('stok_minimum', 0) }}"
                               class="form-input" min="0">
                        @error('stok_minimum') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Safety Stock <span class="text-red-500">*</span></label>
                        <input type="number" name="safety_stock" value="{{ old('safety_stock', 0) }}"
                               class="form-input" min="0">
                        @error('safety_stock') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Harga --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Harga</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Harga Beli (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="harga_beli" value="{{ old('harga_beli', 0) }}"
                               class="form-input" min="0" step="100">
                        @error('harga_beli') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Harga Jual (Rp)</label>
                        <input type="number" name="harga_jual" value="{{ old('harga_jual', 0) }}"
                               class="form-input" min="0" step="100">
                    </div>
                </div>
            </div>

            {{-- Parameter EOQ --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-1 pb-2 border-b">
                    Parameter EOQ
                </h3>
                <p class="text-xs text-gray-500 mb-4">
                    Parameter ini digunakan untuk menghitung EOQ, Safety Stock, dan Reorder Point.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">
                            Biaya Pemesanan (S)
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                            <input type="number" name="ordering_cost"
                                   value="{{ old('ordering_cost', 0) }}"
                                   class="form-input pl-10" min="0" step="1000">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Biaya per kali pesan</p>
                        @error('ordering_cost') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">
                            Biaya Penyimpanan (H)
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                            <input type="number" name="holding_cost"
                                   value="{{ old('holding_cost', 0) }}"
                                   class="form-input pl-10" min="0" step="100">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Per unit per tahun</p>
                        @error('holding_cost') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">
                            Lead Time
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="lead_time"
                                   value="{{ old('lead_time', 1) }}"
                                   class="form-input pr-12" min="1">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">hari</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Waktu tunggu pengiriman</p>
                        @error('lead_time') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Foto & Deskripsi --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">
                    Foto & Deskripsi
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div x-data="{ preview: null }">
                        <label class="form-label">Foto Barang</label>
                        <input type="file" name="foto" accept="image/*"
                               class="form-input"
                               @change="preview = URL.createObjectURL($event.target.files[0])">
                        <img x-show="preview" :src="preview" alt="Preview"
                             class="mt-2 w-32 h-32 object-cover rounded-xl border">
                        @error('foto') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="form-input"
                                  placeholder="Deskripsi barang (opsional)">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="w-4 h-4 text-blue-600 rounded"
                       {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700">Barang Aktif</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan Barang</button>
                <a href="{{ route('admin.items.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection