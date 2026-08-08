<x-guest-layout title="Register">
    <h2 class="text-xl font-bold text-center mb-6">Daftar Anggota</h2>

    <form method="POST" action="/register" class="space-y-4">
        @csrf
        <x-input label="Nama Lengkap" name="name" :required="true" />
        <x-input label="Email" name="email" type="email" :required="true" />
        <x-input label="Password" name="password" type="password" :required="true" />
        <x-button class="w-full">Daftar</x-button>
    </form>

    <p class="text-center mt-4 text-sm text-gray-500">
        Sudah punya akun? <a href="/login" class="text-blue-600 hover:underline">Login</a>
    </p>
</x-guest-layout>
