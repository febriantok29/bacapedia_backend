<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bacapedia - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-700 text-white p-4 shadow">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="/panel" class="text-xl font-bold">Bacapedia</a>
            <div class="flex gap-4 items-center">
                <a href="/panel" class="hover:underline">Dashboard</a>
                <a href="/panel/books" class="hover:underline">Buku</a>
                <a href="/panel/categories" class="hover:underline">Kategori</a>
                @if(session('user_role') === 'Admin')
                <a href="/panel/users" class="hover:underline">Users</a>
                @endif
                <a href="/panel/borrows" class="hover:underline">Peminjaman</a>
                <span class="text-blue-200">|</span>
                <span class="text-sm">{{ session('user_name') }} ({{ session('user_role') }})</span>
                <form method="POST" action="/panel/logout">
                    @csrf
                    <button type="submit" class="bg-red-500 px-3 py-1 rounded text-sm hover:bg-red-600">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>