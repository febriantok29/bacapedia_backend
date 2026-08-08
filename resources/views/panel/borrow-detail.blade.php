@extends('panel.layout')
@section('title', 'Detail Peminjaman')
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="/borrows" class="text-blue-600 hover:underline">&larr; Kembali</a>
    <h1 class="text-2xl font-bold">Detail Peminjaman</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-semibold mb-4">Informasi Peminjaman</h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500">Buku</dt>
                <dd class="font-medium">{{ $borrow->book->title ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Peminjam</dt>
                <dd class="font-medium">{{ $borrow->user->name ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Tanggal Pinjam</dt>
                <dd>{{ $borrow->borrow_date->format('d M Y') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Jatuh Tempo</dt>
                <dd class="{{ $borrow->status === 'Aktif' && $borrow->due_date->lt(now()) ? 'text-red-600 font-bold' : '' }}">{{ $borrow->due_date->format('d M Y') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Tanggal Kembali</dt>
                <dd>{{ $borrow->return_date ? $borrow->return_date->format('d M Y') : '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Status</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-xs {{ $borrow->status === 'Aktif' ? 'bg-yellow-100 text-yellow-700' : ($borrow->status === 'Terlambat' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700') }}">{{ $borrow->status }}</span>
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Denda</dt>
                <dd class="{{ $borrow->penalty > 0 ? 'text-red-600 font-bold' : '' }}">{{ $borrow->penalty > 0 ? 'Rp' . number_format($borrow->penalty, 0, ',', '.') : '-' }}</dd>
            </div>
        </dl>

        @if(in_array(session('user_role'), ['Admin', 'Petugas']) && $borrow->status === 'Aktif')
        <form method="POST" action="/borrows/{{ $borrow->id }}/return" class="mt-4">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full" onclick="return confirm('Proses pengembalian?')">Proses Pengembalian</button>
        </form>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-semibold mb-4">Riwayat Status</h2>
        @if($borrow->histories && count($borrow->histories) > 0)
        <div class="space-y-3">
            @foreach($borrow->histories as $history)
            <div class="border-l-4 {{ $history->status === 'Aktif' ? 'border-yellow-400' : ($history->status === 'Terlambat' ? 'border-red-400' : 'border-green-400') }} pl-3 py-1">
                <p class="text-sm font-medium">{{ $history->status }}</p>
                <p class="text-xs text-gray-500">{{ $history->remarks ?? '-' }}</p>
                <p class="text-xs text-gray-400">{{ $history->created_at ? \Carbon\Carbon::parse($history->created_at)->format('d M Y H:i') : '-' }}</p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-400 text-sm">Belum ada riwayat</p>
        @endif
    </div>
</div>
@endsection
