<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>500 — Server Error</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="text-8xl font-black text-gray-200 mb-4">500</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Terjadi Kesalahan Server</h1>
        <p class="text-gray-500 mb-6">
            Mohon maaf, terjadi kesalahan. Silakan coba lagi beberapa saat.
        </p>
        <a href="{{ route('dashboard') }}" class="btn-primary">← Kembali ke Dashboard</a>
    </div>
</body>
</html>