@extends('panel.layout')
@section('title', 'Kategori')
@section('content')
<h1 class="text-2xl font-bold mb-6">Data Kategori</h1>

@if(in_array(session('user_role'), ['Admin', 'Petugas']))
<form method="POST" action="/categories" class="bg-white rounded-lg shadow p-4 mb-6">
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
                <th class="px-4 py-3 text-left">Jumlah Buku</th>
                @if(in_array(session('user_role'), ['Admin', 'Petugas']))
                <th class="px-4 py-3 text-left">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
            <tr class="border-t">
                <td class="px-4 py-3">{{ $cat->name }}</td>
                <td class="px-4 py-3">{{ $cat->books_count }}</td>
                @if(in_array(session('user_role'), ['Admin', 'Petugas']))
                <td class="px-4 py-3">
                    <form method="POST" action="/categories/{{ $cat->id }}/delete" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('Hapus?')">Hapus</button>
                    </form>
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $categories->links() }}</div>
</div>
@endsection
