<x-layout title="Detail Peminjaman">
    <a href="/borrows" class="text-sm text-blue-600 hover:underline mb-4 inline-block">&larr; Kembali ke daftar</a>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-card title="Informasi Peminjaman">
            <dl class="space-y-3 text-sm">
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
                    <dd class="{{ $borrow->status === 'Aktif' && $borrow->due_date->lt(now()) ? 'text-red-600 font-bold' : '' }}">
                        {{ $borrow->due_date->format('d M Y') }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tanggal Kembali</dt>
                    <dd>{{ $borrow->return_date ? $borrow->return_date->format('d M Y') : '-' }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-gray-500">Status</dt>
                    <dd>
                        @php $variant = match($borrow->status) { 'Aktif' => 'warning', 'Terlambat' => 'danger', default => 'success' }; @endphp
                        <x-badge :variant="$variant">{{ $borrow->status }}</x-badge>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Denda</dt>
                    <dd class="{{ $borrow->penalty > 0 ? 'text-red-600 font-bold' : '' }}">
                        {{ $borrow->penalty > 0 ? 'Rp' . number_format($borrow->penalty, 0, ',', '.') : '-' }}
                    </dd>
                </div>
            </dl>

            @if(in_array(session('user_role'), ['Admin', 'Petugas']) && $borrow->status === 'Aktif')
            <form method="POST" action="/borrows/{{ $borrow->id }}/return" class="mt-5">
                @csrf
                <x-button variant="success" class="w-full" onclick="return confirm('Proses pengembalian?')">Proses Pengembalian</x-button>
            </form>
            @endif
        </x-card>

        <x-card title="Riwayat Status">
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
        </x-card>
    </div>
</x-layout>
