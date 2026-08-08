<x-layout title="Dashboard">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <x-stat-card label="Total Buku" :value="$totalBooks" color="blue" />
        <x-stat-card label="Total Kategori" :value="$totalCategories" color="purple" />
        <x-stat-card label="Total User" :value="$totalUsers" color="green" />
        <x-stat-card label="Sedang Dipinjam" :value="$totalActive" color="yellow" />
        <x-stat-card label="Terlambat (belum kembali)" :value="$totalOverdue" color="red" />
        <x-stat-card label="Dikembalikan Tepat Waktu" :value="$totalReturned" color="green" />
        <x-stat-card label="Dikembalikan Terlambat" :value="$totalLate" color="orange" />
        <x-stat-card label="Total Denda" :value="'Rp' . number_format($totalPenalty, 0, ',', '.')" color="red" />
    </div>
</x-layout>
