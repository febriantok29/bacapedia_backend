<x-guest-layout title="Login">
    <h2 class="text-xl font-bold text-center mb-6">Login</h2>

    <form method="POST" action="/login" class="space-y-4">
        @csrf
        <x-input label="Email" name="credentials" type="email" :required="true" />
        <x-input label="Password" name="password" type="password" :required="true" />
        <x-button class="w-full">Login</x-button>
    </form>

    <p class="text-center mt-4 text-sm text-gray-500">
        Belum punya akun? <a href="/register" class="text-blue-600 hover:underline">Daftar</a>
    </p>
    <p class="text-xs text-gray-400 mt-3 text-center">admin@bacapedia.com / password123</p>
</x-guest-layout>
