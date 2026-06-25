@extends('layouts.app')
@section('title', 'Tambah Supplier')
@section('page-title', 'Tambah Supplier')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('admin.suppliers.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Kode Supplier <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_supplier"
                           value="{{ old('kode_supplier', $kodeDefault) }}"
                           class="form-input @error('kode_supplier') border-red-400 @enderror">
                    @error('kode_supplier') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Nama Supplier <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_supplier" value="{{ old('nama_supplier') }}"
                           class="form-input @error('nama_supplier') border-red-400 @enderror"
                           placeholder="PT / CV / UD ...">
                    @error('nama_supplier') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                           class="form-input" placeholder="Nama PIC">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-input @error('email') border-red-400 @enderror">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}"
                           class="form-input" placeholder="08xx-xxxx-xxxx">
                </div>
                <div>
                    <label class="form-label">Kota</label>
                    <input type="text" name="kota" value="{{ old('kota') }}"
                           class="form-input" placeholder="Jakarta">
                </div>
            </div>
            <div>
                <label class="form-label">Alamat</label>
                <textarea name="alamat" rows="2" class="form-input"
                          placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
            </div>
            <div>
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" rows="2" class="form-input">{{ old('keterangan') }}</textarea>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="w-4 h-4 text-blue-600 rounded"
                       {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700">Supplier Aktif</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('admin.suppliers.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection