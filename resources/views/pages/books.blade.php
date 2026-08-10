<x-layout title="Data Buku">
    @if(in_array(session('user_role'), ['Admin', 'Petugas']))
    <x-card title="Tambah Buku" class="mb-6">
        <form method="POST" action="/books">
            @csrf
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                <select name="category_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm" required>
                    <option value="">Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                    @endforeach
                </select>
                <x-input name="title" placeholder="Judul" :required="true" />
                <x-input name="author" placeholder="Penulis" :required="true" />
                <x-input name="publisher" placeholder="Penerbit" :required="true" />
                <x-input name="published_year" type="number" placeholder="Tahun" :required="true" />
                <div class="flex gap-2">
                    <x-input name="stock" type="number" placeholder="Stok" :required="true" class="w-20" />
                    <x-button>Tambah</x-button>
                </div>
            </div>
        </form>
    </x-card>
    @endif

    <x-card :padding="false">
        <x-table :headers="['Kode', 'Judul', 'Penulis', 'Kategori', 'Tahun', 'Stok', ...(in_array(session('user_role'), ['Admin', 'Petugas']) ? ['Aksi'] : [])]">
            @forelse($books as $book)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs">{{ $book->book_code }}</td>
                <td class="px-4 py-3 font-medium">{{ $book->title }}</td>
                <td class="px-4 py-3">{{ $book->author }}</td>
                <td class="px-4 py-3">{{ $book->category->name ?? '-' }}</td>
                <td class="px-4 py-3">{{ $book->published_year }}</td>
                <td class="px-4 py-3">
                    <x-badge :variant="$book->stock > 0 ? 'success' : 'danger'">{{ $book->stock }}</x-badge>
                </td>
                @if(in_array(session('user_role'), ['Admin', 'Petugas']))
                <td class="px-4 py-3">
                    <details class="inline">
                        <summary class="text-blue-600 cursor-pointer text-xs">Edit</summary>
                        <form method="POST" action="/books/{{ $book->id }}/update" class="mt-2 bg-gray-50 border rounded p-3 w-72 space-y-2">
                            @csrf
                            <label class="block text-xs text-gray-500">Kategori</label>
                            <select name="category_id" class="w-full border rounded px-2 py-1 text-sm" required>
                                @foreach($categories as $cat)
                                <option value="{{ $cat['id'] }}" @selected($cat['id'] == $book->category_id)>{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                            <label class="block text-xs text-gray-500">Judul</label>
                            <input type="text" name="title" value="{{ $book->title }}" placeholder="Judul" class="w-full border rounded px-2 py-1 text-sm" required>
                            <label class="block text-xs text-gray-500">Penulis</label>
                            <input type="text" name="author" value="{{ $book->author }}" placeholder="Penulis" class="w-full border rounded px-2 py-1 text-sm" required>
                            <label class="block text-xs text-gray-500">Penerbit</label>
                            <input type="text" name="publisher" value="{{ $book->publisher }}" placeholder="Penerbit" class="w-full border rounded px-2 py-1 text-sm" required>
                            <label class="block text-xs text-gray-500">Tahun Terbit</label>
                            <input type="number" name="published_year" min="1900" max="{{ date('Y') + 1 }}" value="{{ $book->published_year }}" placeholder="Tahun" class="w-full border rounded px-2 py-1 text-sm" required>
                            <label class="block text-xs text-gray-500">Stok</label>
                            <input type="number" name="stock" min="0" value="{{ $book->stock }}" placeholder="Stok" class="w-full border rounded px-2 py-1 text-sm" required>
                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs">Simpan</button>
                        </form>
                    </details>
                    <form method="POST" action="/books/{{ $book->id }}/delete" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('Hapus buku ini?')">Hapus</button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">Belum ada data buku</td></tr>
            @endforelse
        </x-table>
        <div class="p-4 border-t">{{ $books->appends(request()->except('page'))->links() }}</div>
    </x-card>
</x-layout>
