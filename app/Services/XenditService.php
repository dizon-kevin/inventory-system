<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class XenditService
{
    public function createInvoice(array $payload): Response
    {
        return Http::withToken(config('services.xendit.key'))
            ->acceptJson()
            ->post('https://api.xendit.co/v2/invoices', $payload);
    }

    public function isConfigured(): bool
    {
        return filled(config('services.xendit.key'));
    }
}
