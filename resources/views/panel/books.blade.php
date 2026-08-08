@extends('panel.layout')
@section('title', 'Buku')
@section('content')
<h1 class="text-2xl font-bold mb-6">Data Buku</h1>

<form method="GET" action="/panel/books" class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, penulis, kode" class="w-full border rounded px-3 py-2">
        </div>
        <div class="min-w-40">
            <label class="block text-sm font-medium mb-1">Kategori</label>
            <select name="category_id" class="w-full border rounded px-3 py-2">
                <option value="">Semua</option>
                @foreach($categories as $cat)
                <option value="{{ $cat['id'] }}" @selected(request('category_id') == $cat['id'])>{{ $cat['name'] }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>
        <a href="/panel/books" class="px-4 py-2 rounded border">Reset</a>
    </div>
</form>

@if(session('user_role') === 'Admin')
<form method="POST" action="/panel/books" class="bg-white rounded-lg shadow p-4 mb-6">
    @csrf
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <select name="category_id" class="border rounded px-3 py-2" required>
            <option value="">Kategori</option>
            @foreach($categories as $category)
            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
            @endforeach
        </select>
        <input type="text" name="title" placeholder="Judul" class="border rounded px-3 py-2" required>
        <input type="text" name="author" placeholder="Penulis" class="border rounded px-3 py-2" required>
        <input type="text" name="publisher" placeholder="Penerbit" class="border rounded px-3 py-2" required>
        <input type="number" name="published_year" min="1900" max="{{ date('Y') + 1 }}" placeholder="Tahun" class="border rounded px-3 py-2" required>
        <div class="flex gap-2">
            <input type="number" name="stock" min="0" placeholder="Stok" class="border rounded px-3 py-2 w-20" required>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah</button>
        </div>
    </div>
</form>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Judul</th>
                <th class="px-4 py-3 text-left">Penulis</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">Tahun</th>
                <th class="px-4 py-3 text-left">Stok</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($books as $book)
            <tr class="border-t">
                <td class="px-4 py-3 font-mono text-xs">{{ $book['book_code'] }}</td>
                <td class="px-4 py-3">{{ $book['title'] }}</td>
                <td class="px-4 py-3">{{ $book['author'] }}</td>
                <td class="px-4 py-3">{{ $book['category']['name'] ?? '-' }}</td>
                <td class="px-4 py-3">{{ $book['published_year'] }}</td>
                <td class="px-4 py-3">{{ $book['stock'] }}</td>
                <td class="px-4 py-3">
                    @if(session('user_role') === 'Admin')
                    <details class="inline">
                        <summary class="text-blue-600 cursor-pointer text-xs">Edit</summary>
                        <form method="POST" action="/panel/books/{{ $book['id'] }}/update" class="mt-2 bg-gray-50 border rounded p-3 w-72">
                            @csrf
                            <div class="space-y-2">
                                <select name="category_id" class="w-full border rounded px-2 py-1" required>
                                    @foreach($categories as $category)
                                    <option value="{{ $category['id'] }}" @selected($category['id'] == $book['category_id'])>{{ $category['name'] }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="title" value="{{ $book['title'] }}" class="w-full border rounded px-2 py-1" required>
                                <input type="text" name="author" value="{{ $book['author'] }}" class="w-full border rounded px-2 py-1" required>
                                <input type="text" name="publisher" value="{{ $book['publisher'] }}" class="w-full border rounded px-2 py-1" required>
                                <input type="number" name="published_year" min="1900" max="{{ date('Y') + 1 }}" value="{{ $book['published_year'] }}" class="w-full border rounded px-2 py-1" required>
                                <input type="number" name="stock" min="0" value="{{ $book['stock'] }}" class="w-full border rounded px-2 py-1" required>
                                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs">Simpan</button>
                            </div>
                        </form>
                    </details>
                    <form method="POST" action="/panel/books/{{ $book['id'] }}/delete" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('Hapus buku ini?')">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada data buku</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @include('panel.pager', compact('metadata', 'query'))
</div>
@endsection