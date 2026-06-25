@extends('layouts.app')
@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kategori"
                       value="{{ old('nama_kategori', $category->nama_kategori) }}"
                       class="form-input @error('nama_kategori') border-red-400 @enderror">
                @error('nama_kategori')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Kode Kategori</label>
                <input type="text" name="kode_kategori"
                       value="{{ old('kode_kategori', $category->kode_kategori) }}"
                       class="form-input @error('kode_kategori') border-red-400 @enderror">
                @error('kode_kategori')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="form-input">{{ old('deskripsi', $category->deskripsi) }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="w-4 h-4 text-blue-600 rounded"
                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700">Kategori Aktif</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Update</button>
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection