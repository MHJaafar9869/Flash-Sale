<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Traits\ResponseJson;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! \in_array($request->method(), ['POST', 'PATCH', 'PUT'])) {
            return $next($request);
        }

        $key = $request->header('Idempotency-Key');

        if (! $key || \strlen($key) < 10) {
            return $this->respondError('Invalid Idempotency-Key header', 400);
        }

        $requestBody = $request->getContent();
        $payload = json_decode($requestBody, true);

        if (\is_array($payload)) {
            ksort($payload);
            $fingerprint = hash('sha256', json_encode($payload));
        } else {
            $fingerprint = hash('sha256', $requestBody);
        }

        $fullKey = "idempotency:{$key}:{$fingerprint}";

        if ($cached = Cache::get($fullKey)) {
            return response($cached['body'], $cached['status'])
                ->withHeaders($cached['headers']);
        }

        $response = $next($request);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            Cache::put($fullKey, [
                'status' => $response->getStatusCode(),
                'body' => $response->getContent(),
                'headers' => $response->headers->all(),
            ], now()->addHours(24));
        }

        return $response;
    }
}
