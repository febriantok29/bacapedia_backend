<x-layout title="Data User">
    <x-card title="Tambah User" class="mb-6">
        <form method="POST" action="/users">
            @csrf
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <x-input name="name" placeholder="Nama" :required="true" />
                <x-input name="email" type="email" placeholder="Email" :required="true" />
                <x-input name="password" type="password" placeholder="Password" :required="true" />
                <select name="role" class="border border-gray-300 rounded-md px-3 py-2 text-sm" required>
                    <option value="Anggota">Anggota</option>
                    <option value="Petugas">Petugas</option>
                    <option value="Admin">Admin</option>
                </select>
                <x-button>Tambah</x-button>
            </div>
        </form>
    </x-card>

    <x-card :padding="false">
        <x-table :headers="['Kode', 'Nama', 'Email', 'Role', 'Aksi']">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs">{{ $user->user_code }}</td>
                <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                <td class="px-4 py-3">{{ $user->email }}</td>
                <td class="px-4 py-3">
                    <x-badge :variant="$user->role === 'Admin' ? 'danger' : ($user->role === 'Petugas' ? 'warning' : 'info')">{{ $user->role }}</x-badge>
                </td>
                <td class="px-4 py-3">
                    @if($user->id !== session('user_id'))
                    <form method="POST" action="/users/{{ $user->id }}/delete" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('Hapus user ini?')">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">Belum ada user</td></tr>
            @endforelse
        </x-table>
        <div class="p-4 border-t">{{ $users->links() }}</div>
    </x-card>
</x-layout>
