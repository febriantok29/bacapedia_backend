<x-layout title="Dashboard">
    <div class="mb-6">
        @if($isMember)
            <h1 class="text-xl font-semibold text-gray-800">Dashboard Saya</h1>
            <p class="text-sm text-gray-500">Data pribadi Anda</p>
        @else
            <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
        @endif
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @if(!$isMember)
        <x-stat-card label="Total Buku" :value="$totalBooks" color="blue" />
        <x-stat-card label="Total Kategori" :value="$totalCategories" color="purple" />
        @if(session('user_role') === 'Admin')
        <x-stat-card label="Total User" :value="$totalUsers" color="green" />
        @endif
        @else
        <x-stat-card label="Total Buku" :value="$totalBooks" color="blue" />
        @endif

        <x-stat-card label="Sedang Dipinjam" :value="$totalActive" color="yellow" />
        <x-stat-card label="Terlambat (belum kembali)" :value="$totalOverdue" color="red" />
        <x-stat-card label="Dikembalikan Tepat Waktu" :value="$totalReturned" color="green" />
        <x-stat-card label="Dikembalikan Terlambat" :value="$totalLate" color="orange" />
        <x-stat-card label="{{ $isMember ? 'Total Denda Saya' : 'Total Denda' }}" :value="'Rp' . number_format($totalPenalty, 0, ',', '.')" color="red" />
    </div>
</x-layout>