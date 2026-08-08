<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Carbon\Carbon;

class JwtService
{
    private string $secret;
    private int $accessTtl;
    private int $refreshTtl;
    private string $algorithm = 'HS256';

    public function __construct()
    {
        $this->secret = env('JWT_SECRET');
        $this->accessTtl = 900;
        $this->refreshTtl = 604800;
    }

    public function issueAccessToken(array $user, array $options = []): string
    {
        $ttl = $options['access_token_ttl'] ?? $this->accessTtl;

        $payload = [
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addSeconds($ttl)->timestamp,
            'type' => 'access',
            'guard' => 'user',
            'sub' => $user['id'],
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function issueRefreshToken(array $user, array $options = []): string
    {
        $ttl = $options['refresh_token_ttl'] ?? $this->refreshTtl;

        $payload = [
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addSeconds($ttl)->timestamp,
            'type' => 'refresh',
            'guard' => 'user',
            'sub' => $user['id'],
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function verify(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (ExpiredException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function isExpired(string $token): bool
    {
        try {
            JWT::decode($token, new Key($this->secret, $this->algorithm));
            return false;
        } catch (ExpiredException $e) {
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function issueTokenPair(array $user, array $options = []): array
    {
        if (app()->environment('local') && config('app.debug') && isset($options['is_debug']) && $options['is_debug']) {
            return [
                'access_token' => $this->issueAccessToken($user, $options),
                'refresh_token' => $this->issueRefreshToken($user, $options),
                'token_type' => 'Bearer',
                'expires_in' => $options['access_token_ttl'] ?? $this->accessTtl,
            ];
        }

        return [
            'access_token' => $this->issueAccessToken($user),
            'refresh_token' => $this->issueRefreshToken($user),
            'token_type' => 'Bearer',
            'expires_in' => $this->accessTtl,
        ];
    }

    public function refreshAccessToken(string $refreshToken): ?array
    {
        $decoded = $this->verify($refreshToken);

        if (!$decoded || $decoded->type !== 'refresh') {
            return null;
        }

        $user = [
            'id' => $decoded->sub,
            'email' => '',
            'role' => '',
        ];

        return [
            'access_token' => $this->issueAccessToken($user),
            'token_type' => 'Bearer',
            'expires_in' => $this->accessTtl,
        ];
    }
}
