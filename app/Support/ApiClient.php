<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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

        return $this->handleResponse($response);
    }

    public function post(string $path, array $data = []): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl() . $path, $data);

        return $this->handleResponse($response);
    }

    public function put(string $path, array $data = []): array
    {
        $response = Http::withHeaders($this->headers())
            ->put($this->baseUrl() . $path, $data);

        return $this->handleResponse($response);
    }

    public function delete(string $path): array
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->baseUrl() . $path);

        return $this->handleResponse($response);
    }

    private function handleResponse(\Illuminate\Http\Client\Response $response): array
    {
        if ($response->status() === 401 && Session::has('access_token')) {
            Session::flush();
            abort(redirect('/login')->with('error', 'Sesi telah berakhir, silakan login kembali'));
        }

        return $response->json() ?? [];
    }

    public function paginated(string $path, array $query, Request $request): LengthAwarePaginator
    {
        $response = $this->get($path, $query);

        if (($response['success'] ?? false) !== true) {
            return new LengthAwarePaginator([], 0, 15, 1, ['path' => $request->url(), 'query' => $request->query()]);
        }

        $items = $response['data'] ?? [];
        $metadata = $response['metadata'] ?? [];

        return new LengthAwarePaginator(
            collect($items)->map(fn($item) => $this->toObject($item)),
            $metadata['total'] ?? count($items),
            $metadata['per_page'] ?? 15,
            $metadata['current_page'] ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    public function toObject(mixed $data): object
    {
        return json_decode(json_encode($data));
    }
}
