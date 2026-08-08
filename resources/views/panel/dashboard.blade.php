@extends('panel.layout')
@section('title', 'Dashboard')
@section('content')
<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total Buku</p>
        <p class="text-3xl font-bold text-blue-600">{{ $totalBooks }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total Kategori</p>
        <p class="text-3xl font-bold text-purple-600">{{ $totalCategories }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total User</p>
        <p class="text-3xl font-bold text-green-600">{{ $totalUsers }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Sedang Dipinjam</p>
        <p class="text-3xl font-bold text-yellow-600">{{ $totalActive }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Terlambat (belum kembali)</p>
        <p class="text-3xl font-bold text-red-600">{{ $totalOverdue }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Dikembalikan Tepat Waktu</p>
        <p class="text-3xl font-bold text-green-600">{{ $totalReturned }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Dikembalikan Terlambat</p>
        <p class="text-3xl font-bold text-orange-600">{{ $totalLate }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total Denda Terkumpul</p>
        <p class="text-3xl font-bold text-red-600">Rp{{ number_format($totalPenalty, 0, ',', '.') }}</p>
    </div>
</div>
@endsection
