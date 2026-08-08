<x-layout title="Data Kategori">
    @if(in_array(session('user_role'), ['Admin', 'Petugas']))
    <x-card title="Tambah Kategori" class="mb-6">
        <form method="POST" action="/categories">
            @csrf
            <div class="flex gap-3">
                <x-input name="name" placeholder="Nama kategori baru" :required="true" class="flex-1" />
                <x-button>Tambah</x-button>
            </div>
        </form>
    </x-card>
    @endif

    <x-card :padding="false">
        <x-table :headers="['Nama', 'Jumlah Buku', ...(in_array(session('user_role'), ['Admin', 'Petugas']) ? ['Aksi'] : [])]">
            @forelse($categories as $cat)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $cat->name }}</td>
                <td class="px-4 py-3">{{ $cat->books_count ?? '-' }}</td>
                @if(in_array(session('user_role'), ['Admin', 'Petugas']))
                <td class="px-4 py-3">
                    <form method="POST" action="/categories/{{ $cat->id }}/delete" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-12 text-center text-gray-400">Belum ada kategori</td></tr>
            @endforelse
        </x-table>
        <div class="p-4 border-t">{{ $categories->appends(request()->except('page'))->links() }}</div>
    </x-card>
</x-layout>
