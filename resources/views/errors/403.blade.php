<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>403 — Akses Ditolak</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="text-8xl font-black text-gray-200 mb-4">403</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Akses Ditolak</h1>
        <p class="text-gray-500 mb-6">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="{{ url()->previous() }}" class="btn-primary">← Kembali</a>
    </div>
</body>
</html>