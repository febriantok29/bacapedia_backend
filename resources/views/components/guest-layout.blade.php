@props(['title' => 'Bacapedia'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Bacapedia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">📚 Bacapedia</h1>
            <p class="text-sm text-gray-500 mt-1">Perpustakaan Digital Kota Sejahtera</p>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-8">
            <x-alert />
            {{ $slot }}
        </div>
    </div>
</body>
</html>
