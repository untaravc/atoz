<?php

namespace App\Support;

use InvalidArgumentException;
use RuntimeException;

class JwtToken
{
    public static function secret(): string
    {
        $secret = env('JWT_SECRET');
        if (is_string($secret) && strlen($secret) > 0) {
            return $secret;
        }

        $appKey = config('app.key');
        if (!is_string($appKey) || $appKey === '') {
            throw new RuntimeException('JWT secret is not configured.');
        }

        if (str_starts_with($appKey, 'base64:')) {
            $decoded = base64_decode(substr($appKey, 7), true);
            if ($decoded === false) {
                throw new RuntimeException('Invalid APP_KEY base64 value.');
            }
            return $decoded;
        }

        return $appKey;
    }

    public static function encode(array $payload, ?int $expiresInSeconds = null): string
    {
        $now = time();
        $payload = array_merge([
            'iat' => $now,
        ], $payload);

        if ($expiresInSeconds !== null) {
            $payload['exp'] = $now + $expiresInSeconds;
        }

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $segments = [
            self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES)),
            self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES)),
        ];

        $signature = self::sign(implode('.', $segments), self::secret());
        $segments[] = self::base64UrlEncode($signature);

        return implode('.', $segments);
    }

    /**
     * @return array<string, mixed>
     */
    public static function decode(string $token): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Invalid token format.');
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $header = json_decode(self::base64UrlDecode($encodedHeader), true);
        if (!is_array($header) || ($header['alg'] ?? null) !== 'HS256') {
            throw new InvalidArgumentException('Invalid token header.');
        }

        $payload = json_decode(self::base64UrlDecode($encodedPayload), true);
        if (!is_array($payload)) {
            throw new InvalidArgumentException('Invalid token payload.');
        }

        $signature = self::base64UrlDecode($encodedSignature);
        $expected = self::sign($encodedHeader . '.' . $encodedPayload, self::secret());

        if (!hash_equals($expected, $signature)) {
            throw new InvalidArgumentException('Invalid token signature.');
        }

        $now = time();
        if (isset($payload['exp']) && is_numeric($payload['exp']) && (int) $payload['exp'] < $now) {
            throw new InvalidArgumentException('Token expired.');
        }

        return $payload;
    }

    private static function sign(string $data, string $secret): string
    {
        return hash_hmac('sha256', $data, $secret, true);
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);
        if ($decoded === false) {
            throw new InvalidArgumentException('Invalid base64url encoding.');
        }
        return $decoded;
    }
}

