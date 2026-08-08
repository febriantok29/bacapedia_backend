<x-layout title="Data Peminjaman">
    <x-card title="Filter" class="mb-6">
        <form method="GET" action="/borrows">
            <div class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="">Semua</option>
                        @foreach(['Aktif', 'Dikembalikan', 'Terlambat'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                @if($isStaff)
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Anggota</label>
                    <select name="user_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
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
                <x-button variant="secondary" type="submit">Filter</x-button>
                <a href="/borrows" class="text-sm text-gray-500 hover:underline py-2">Reset</a>
            </div>
        </form>
    </x-card>

    <x-card title="Pinjam Buku" class="mb-6">
        <form method="POST" action="/borrows">
            @csrf
            <div class="flex flex-wrap gap-3 items-end mb-3">
                @if($isStaff)
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Atas nama</label>
                    <select name="user_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="">Diri sendiri</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Tanggal pinjam (opsional)</label>
                    <input type="date" name="borrow_date" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                <x-button>Pinjam</x-button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto border rounded-lg p-3 bg-gray-50">
                @foreach($books as $book)
                <label class="flex items-center gap-2 text-sm p-2 hover:bg-white rounded cursor-pointer transition">
                    <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" class="rounded">
                    <span class="truncate">{{ $book->title }}</span>
                    <span class="text-gray-400 text-xs ml-auto">({{ $book->stock }})</span>
                </label>
                @endforeach
            </div>
        </form>
    </x-card>

    <x-card :padding="false">
        <x-table :headers="['Buku', 'Anggota', 'Tgl Pinjam', 'Jatuh Tempo', 'Status', 'Denda', 'Aksi']">
            @forelse($borrows as $borrow)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $borrow->book->title ?? '-' }}</td>
                <td class="px-4 py-3">{{ $borrow->user->name ?? '-' }}</td>
                <td class="px-4 py-3 text-xs">{{ $borrow->borrow_date->format('d M Y') }}</td>
                <td class="px-4 py-3 text-xs {{ $borrow->status === 'Aktif' && $borrow->due_date->lt(now()) ? 'text-red-600 font-bold' : '' }}">
                    {{ $borrow->due_date->format('d M Y') }}
                </td>
                <td class="px-4 py-3">
                    @php $variant = match($borrow->status) { 'Aktif' => 'warning', 'Terlambat' => 'danger', default => 'success' }; @endphp
                    <x-badge :variant="$variant">{{ $borrow->status }}</x-badge>
                </td>
                <td class="px-4 py-3 text-xs">{{ $borrow->penalty > 0 ? 'Rp' . number_format($borrow->penalty, 0, ',', '.') : '-' }}</td>
                <td class="px-4 py-3 flex gap-2">
                    <a href="/borrows/{{ $borrow->id }}" class="text-blue-600 hover:underline text-xs">Detail</a>
                    @if($isStaff && $borrow->status === 'Aktif')
                    <form method="POST" action="/borrows/{{ $borrow->id }}/return" class="inline">
                        @csrf
                        <button type="submit" class="text-green-600 hover:underline text-xs" onclick="return confirm('Kembalikan?')">Return</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">Belum ada data peminjaman</td></tr>
            @endforelse
        </x-table>
        <div class="p-4 border-t">{{ $borrows->appends(request()->except('page'))->links() }}</div>
    </x-card>
</x-layout>
