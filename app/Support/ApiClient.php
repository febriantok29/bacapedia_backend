<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiClient
{
    private function baseUrl(): string
    {
        return rtrim(config('app.url'), '/') . '/api/v1';
    }

    private function headers(): array
    {
        $headers = ['Accept' => 'application/json'];

        $token = Session::get('access_token');
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }

    public function get(string $path, array $query = []): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl() . $path, $query);

        return $response->json() ?? [];
    }

    public function post(string $path, array $data = []): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl() . $path, $data);

        return $response->json() ?? [];
    }

    public function put(string $path, array $data = []): array
    {
        $response = Http::withHeaders($this->headers())
            ->put($this->baseUrl() . $path, $data);

        return $response->json() ?? [];
    }

    public function delete(string $path): array
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->baseUrl() . $path);

        return $response->json() ?? [];
    }
}
