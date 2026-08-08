@extends('panel.layout')
@section('title', 'Kategori')
@section('content')
<h1 class="text-2xl font-bold mb-6">Data Kategori</h1>

<form method="GET" action="/panel/categories" class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex gap-3 items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama kategori" class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>
        <a href="/panel/categories" class="px-4 py-2 rounded border">Reset</a>
    </div>
</form>

@if(session('user_role') === 'Admin')
<form method="POST" action="/panel/categories" class="bg-white rounded-lg shadow p-4 mb-6">
    @csrf
    <div class="flex gap-3">
        <input type="text" name="name" placeholder="Nama kategori baru" class="border rounded px-3 py-2 flex-1" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah</button>
    </div>
</form>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr class="border-t">
                <td class="px-4 py-3">{{ $category['name'] }}</td>
                <td class="px-4 py-3">
                    @if(session('user_role') === 'Admin')
                    <details class="inline">
                        <summary class="text-blue-600 cursor-pointer text-xs">Edit</summary>
                        <form method="POST" action="/panel/categories/{{ $category['id'] }}/update" class="mt-2 bg-gray-50 border rounded p-3 w-72 space-y-2">
                            @csrf
                            <input type="text" name="name" value="{{ $category['name'] }}" class="w-full border rounded px-2 py-1" required>
                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs">Simpan</button>
                        </form>
                    </details>
                    <form method="POST" action="/panel/categories/{{ $category['id'] }}/delete" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="px-4 py-8 text-center text-gray-400">Belum ada data kategori</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @include('panel.pager', compact('metadata', 'query'))
</div>
@endsection