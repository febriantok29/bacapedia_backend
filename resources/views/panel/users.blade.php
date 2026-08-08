@extends('panel.layout')
@section('title', 'Users')
@section('content')
<h1 class="text-2xl font-bold mb-6">Data User</h1>

<form method="GET" action="/panel/users" class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, email, kode" class="w-full border rounded px-3 py-2">
        </div>
        <div class="min-w-36">
            <label class="block text-sm font-medium mb-1">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2">
                <option value="">Semua</option>
                @foreach(['Anggota', 'Petugas', 'Admin'] as $role)
                <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>
        <a href="/panel/users" class="px-4 py-2 rounded border">Reset</a>
    </div>
</form>

<form method="POST" action="/panel/users" class="bg-white rounded-lg shadow p-4 mb-6">
    @csrf
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <input type="text" name="name" placeholder="Nama" class="border rounded px-3 py-2" required>
        <input type="email" name="email" placeholder="Email" class="border rounded px-3 py-2" required>
        <input type="password" name="password" minlength="6" placeholder="Password" class="border rounded px-3 py-2" required>
        <select name="role" class="border rounded px-3 py-2" required>
            <option value="Anggota">Anggota</option>
            <option value="Petugas">Petugas</option>
            <option value="Admin">Admin</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah</button>
    </div>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Role</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="border-t">
                <td class="px-4 py-3 font-mono text-xs">{{ $user['user_code'] }}</td>
                <td class="px-4 py-3">{{ $user['name'] }}</td>
                <td class="px-4 py-3">{{ $user['email'] }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs {{ $user['role'] === 'Admin' ? 'bg-red-100 text-red-700' : ($user['role'] === 'Petugas' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">{{ $user['role'] }}</span>
                </td>
                <td class="px-4 py-3">
                    <details class="inline">
                        <summary class="text-blue-600 cursor-pointer text-xs">Edit</summary>
                        <form method="POST" action="/panel/users/{{ $user['id'] }}/update" class="mt-2 bg-gray-50 border rounded p-3 w-72 space-y-2">
                            @csrf
                            <input type="text" name="name" value="{{ $user['name'] }}" class="w-full border rounded px-2 py-1" required>
                            <input type="email" name="email" value="{{ $user['email'] }}" class="w-full border rounded px-2 py-1" required>
                            <select name="role" class="w-full border rounded px-2 py-1" required>
                                @foreach(['Anggota', 'Petugas', 'Admin'] as $role)
                                <option value="{{ $role }}" @selected($user['role'] === $role)>{{ $role }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs">Simpan</button>
                        </form>
                    </details>
                    <details class="inline">
                        <summary class="text-yellow-600 cursor-pointer text-xs">Reset Password</summary>
                        <form method="POST" action="/panel/users/{{ $user['id'] }}/reset-password" class="mt-2 bg-gray-50 border rounded p-3 w-72 space-y-2">
                            @csrf
                            <input type="password" name="password" minlength="6" placeholder="Password baru" class="w-full border rounded px-2 py-1" required>
                            <button type="submit" class="bg-yellow-600 text-white px-3 py-1 rounded text-xs">Reset</button>
                        </form>
                    </details>
                    <form method="POST" action="/panel/users/{{ $user['id'] }}/delete" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('Hapus user ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada data user</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @include('panel.pager', compact('metadata', 'query'))
</div>
@endsection