<x-layout title="Data Buku">
    @if(in_array(session('user_role'), ['Admin', 'Petugas']))
    <x-card title="Tambah Buku" class="mb-6">
        <form method="POST" action="/books">
            @csrf
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                <select name="category_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm" required>
                    <option value="">Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
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
        <div class="p-4 border-t">{{ $books->links() }}</div>
    </x-card>
</x-layout>
