@extends('panel.layout')
@section('title', 'Buku')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Data Buku</h1>
</div>

@if(in_array(session('user_role'), ['Admin', 'Petugas']))
<form method="POST" action="/books" class="bg-white rounded-lg shadow p-4 mb-6">
    @csrf
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <select name="category_id" class="border rounded px-3 py-2" required>
            <option value="">Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <input type="text" name="title" placeholder="Judul" class="border rounded px-3 py-2" required>
        <input type="text" name="author" placeholder="Penulis" class="border rounded px-3 py-2" required>
        <input type="text" name="publisher" placeholder="Penerbit" class="border rounded px-3 py-2" required>
        <input type="number" name="published_year" placeholder="Tahun" class="border rounded px-3 py-2" required>
        <div class="flex gap-2">
            <input type="number" name="stock" placeholder="Stok" class="border rounded px-3 py-2 w-20" required>
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
                @if(in_array(session('user_role'), ['Admin', 'Petugas']))
                <th class="px-4 py-3 text-left">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
            <tr class="border-t">
                <td class="px-4 py-3 font-mono text-xs">{{ $book->book_code }}</td>
                <td class="px-4 py-3">{{ $book->title }}</td>
                <td class="px-4 py-3">{{ $book->author }}</td>
                <td class="px-4 py-3">{{ $book->category->name ?? '-' }}</td>
                <td class="px-4 py-3">{{ $book->published_year }}</td>
                <td class="px-4 py-3">{{ $book->stock }}</td>
                @if(in_array(session('user_role'), ['Admin', 'Petugas']))
                <td class="px-4 py-3">
                    <form method="POST" action="/books/{{ $book->id }}/delete" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('Hapus?')">Hapus</button>
                    </form>
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $books->links() }}</div>
</div>
@endsection
