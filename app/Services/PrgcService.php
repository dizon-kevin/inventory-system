<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PrgcService
{
    public function sendData(array $payload)
    {
        return Http::withToken(config('services.prgc.key'))
            ->post('https://api.prgc.example.com/v1/data', $payload);
    }
}
