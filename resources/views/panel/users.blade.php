@extends('panel.layout')
@section('title', 'Users')
@section('content')
<h1 class="text-2xl font-bold mb-6">Data User</h1>

<form method="POST" action="/users" class="bg-white rounded-lg shadow p-4 mb-6">
    @csrf
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <input type="text" name="name" placeholder="Nama" class="border rounded px-3 py-2" required>
        <input type="email" name="email" placeholder="Email" class="border rounded px-3 py-2" required>
        <input type="password" name="password" placeholder="Password" class="border rounded px-3 py-2" required>
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
            @foreach($users as $user)
            <tr class="border-t">
                <td class="px-4 py-3 font-mono text-xs">{{ $user->user_code }}</td>
                <td class="px-4 py-3">{{ $user->name }}</td>
                <td class="px-4 py-3">{{ $user->email }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs {{ $user->role === 'Admin' ? 'bg-red-100 text-red-700' : ($user->role === 'Petugas' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ $user->role }}
                    </span>
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
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $users->links() }}</div>
</div>
@endsection
