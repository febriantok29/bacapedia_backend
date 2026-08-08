@extends('panel.layout')
@section('title', 'Peminjaman')
@section('content')
<h1 class="text-2xl font-bold mb-6">Data Peminjaman</h1>

<form method="GET" action="/borrows" class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="min-w-40">
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="">Semua</option>
                @foreach(['Aktif', 'Dikembalikan', 'Terlambat'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        @if($isStaff)
        <div class="min-w-44">
            <label class="block text-sm font-medium mb-1">Anggota</label>
            <select name="user_id" class="w-full border rounded px-3 py-2">
                <option value="">Semua</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <label class="flex items-center gap-2 text-sm py-2">
            <input type="checkbox" name="is_overdue" value="1" @checked(request()->boolean('is_overdue'))>
            Hanya terlambat
        </label>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>
        <a href="/borrows" class="px-4 py-2 rounded border">Reset</a>
    </div>
</form>

<form method="POST" action="/borrows" class="bg-white rounded-lg shadow p-4 mb-6">
    @csrf
    <h2 class="text-sm font-semibold mb-3">Pinjam Buku (bisa pilih lebih dari 1)</h2>
    <div class="flex flex-wrap gap-3 items-end">
        @if($isStaff)
        <div class="min-w-48">
            <label class="block text-sm font-medium mb-1">Atas nama</label>
            <select name="user_id" class="w-full border rounded px-3 py-2">
                <option value="">Diri sendiri</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="min-w-44">
            <label class="block text-sm font-medium mb-1">Tanggal pinjam (opsional)</label>
            <input type="date" name="borrow_date" class="border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Pinjam</button>
    </div>
    <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-2 max-h-60 overflow-y-auto border rounded p-3">
        @foreach($books as $book)
        <label class="flex items-center gap-2 text-sm p-2 hover:bg-gray-50 rounded cursor-pointer">
            <input type="checkbox" name="book_ids[]" value="{{ $book->id }}">
            <span>{{ $book->title }}</span>
            <span class="text-gray-400 text-xs">(stok {{ $book->stock }})</span>
        </label>
        @endforeach
    </div>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Buku</th>
                <th class="px-4 py-3 text-left">Anggota</th>
                <th class="px-4 py-3 text-left">Tanggal Pinjam</th>
                <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Denda</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($borrows as $borrow)
            <tr class="border-t">
                <td class="px-4 py-3">{{ $borrow->book->title ?? '-' }}</td>
                <td class="px-4 py-3">{{ $borrow->user->name ?? '-' }}</td>
                <td class="px-4 py-3">{{ $borrow->borrow_date->format('d M Y') }}</td>
                <td class="px-4 py-3">
                    <span class="{{ $borrow->status === 'Aktif' && $borrow->due_date->lt(now()) ? 'text-red-600 font-bold' : '' }}">
                        {{ $borrow->due_date->format('d M Y') }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs {{ $borrow->status === 'Aktif' ? 'bg-yellow-100 text-yellow-700' : ($borrow->status === 'Terlambat' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700') }}">{{ $borrow->status }}</span>
                </td>
                <td class="px-4 py-3">{{ $borrow->penalty > 0 ? 'Rp' . number_format($borrow->penalty, 0, ',', '.') : '-' }}</td>
                <td class="px-4 py-3">
                    <a href="/borrows/{{ $borrow->id }}" class="text-blue-600 hover:underline text-xs mr-3">Detail</a>
                    @if($isStaff && $borrow->status === 'Aktif')
                    <form method="POST" action="/borrows/{{ $borrow->id }}/return" class="inline">
                        @csrf
                        <button type="submit" class="text-green-600 hover:underline text-xs" onclick="return confirm('Kembalikan buku ini?')">Kembalikan</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada data peminjaman</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $borrows->appends(request()->except('page'))->links() }}</div>
</div>
@endsection
