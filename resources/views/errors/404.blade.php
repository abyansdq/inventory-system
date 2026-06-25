<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>404 — Halaman Tidak Ditemukan</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="text-8xl font-black text-gray-200 mb-4">404</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Halaman Tidak Ditemukan</h1>
        <p class="text-gray-500 mb-6">Halaman yang Anda cari tidak ada atau sudah dipindahkan.</p>
        <a href="{{ route('dashboard') }}" class="btn-primary">← Kembali ke Dashboard</a>
    </div>
</body>
</html>