<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CheckoutAddressController extends Controller
{
    public function regions(): JsonResponse
    {
        return response()->json($this->requestPsgc('/regions'));
    }

    public function provinces(string $regionCode): JsonResponse
    {
        return response()->json($this->requestPsgc("/regions/{$regionCode}/provinces"));
    }

    public function cities(string $regionCode): JsonResponse
    {
        $provinceCode = request()->query('province');

        $path = filled($provinceCode)
            ? "/provinces/{$provinceCode}/cities-municipalities"
            : "/regions/{$regionCode}/cities-municipalities";

        return response()->json($this->requestPsgc($path));
    }

    public function barangays(string $cityCode): JsonResponse
    {
        return response()->json($this->requestPsgc("/cities-municipalities/{$cityCode}/barangays"));
    }

    protected function requestPsgc(string $path): array
    {
        return Cache::remember(
            'checkout:psgc:' . md5($path),
            now()->addHours(12),
            function () use ($path): array {
                $response = Http::acceptJson()
                    ->timeout(10)
                    ->get(rtrim((string) config('checkout.psgc_base_url'), '/') . $path);

                $items = $response->successful() ? $response->json() : [];

                return collect($items)
                    ->map(fn (array $item) => [
                        'code' => (string) ($item['code'] ?? ''),
                        'name' => Str::of((string) ($item['name'] ?? ''))->squish()->toString(),
                    ])
                    ->filter(fn (array $item) => $item['code'] !== '' && $item['name'] !== '')
                    ->values()
                    ->all();
            }
        );
    }
}
