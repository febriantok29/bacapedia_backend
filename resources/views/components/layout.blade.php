@props(['title' => 'Bacapedia'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Bacapedia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { sidebar: '#1e293b', 'sidebar-hover': '#334155' } } } }</script>
</head>
<body class="bg-gray-100 min-h-screen flex">
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-sidebar text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-200 flex flex-col">
        <div class="p-5 border-b border-gray-700">
            <a href="/" class="text-xl font-bold tracking-wide">📚 Bacapedia</a>
            <p class="text-xs text-gray-400 mt-1">Perpustakaan Digital</p>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto">
            <x-sidebar-link href="/" icon="🏠" :active="request()->is('/')">Dashboard</x-sidebar-link>
            <x-sidebar-link href="/books" icon="📖" :active="request()->is('books*')">Buku</x-sidebar-link>
            <x-sidebar-link href="/categories" icon="🏷️" :active="request()->is('categories*')">Kategori</x-sidebar-link>
            <x-sidebar-link href="/borrows" icon="🔄" :active="request()->is('borrows*')">Peminjaman</x-sidebar-link>
            @if(session('user_role') === 'Admin')
            <div class="mt-4 px-5 mb-2">
                <span class="text-xs text-gray-500 uppercase tracking-wider">Admin</span>
            </div>
            <x-sidebar-link href="/users" icon="👥" :active="request()->is('users*')">Users</x-sidebar-link>
            @endif
        </nav>

        <div class="p-4 border-t border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ session('user_name') }}</p>
                    <p class="text-xs text-gray-400">{{ session('user_role') }}</p>
                </div>
            </div>
        </div>
    </aside>

    <div id="overlay" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
        <header class="bg-white shadow-sm border-b px-6 py-3 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-800">{{ $title }}</h1>
            </div>
            <div class="flex items-center gap-4">
                <x-badge :variant="session('user_role') === 'Admin' ? 'danger' : (session('user_role') === 'Petugas' ? 'warning' : 'info')">{{ session('user_role') }}</x-badge>
                <form method="POST" action="/logout" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-red-600">Logout</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-6">
            <x-alert />
            {{ $slot }}
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('overlay').classList.toggle('hidden');
        }
    </script>
</body>
</html>
