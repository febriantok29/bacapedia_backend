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
        <p class="text-3xl font-bold text-indigo-600">{{ $totalCategories }}</p>
    </div>
    @if(session('user_role') === 'Admin')
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total User</p>
        <p class="text-3xl font-bold text-green-600">{{ $totalUsers }}</p>
    </div>
    @endif
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Sedang Dipinjam</p>
        <p class="text-3xl font-bold text-yellow-600">{{ $summary['total_active'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Terlambat (belum kembali)</p>
        <p class="text-3xl font-bold text-red-600">{{ $summary['total_overdue'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Dikembalikan</p>
        <p class="text-3xl font-bold text-green-600">{{ $summary['total_returned'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Dikembalikan Terlambat</p>
        <p class="text-3xl font-bold text-orange-600">{{ $summary['total_late_returned'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 col-span-2">
        <p class="text-sm text-gray-500">Total Denda Terkumpul</p>
        <p class="text-3xl font-bold text-red-600">Rp{{ number_format($summary['total_penalty_collected'] ?? 0, 0, ',', '.') }}</p>
    </div>
</div>
@endsection