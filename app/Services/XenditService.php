<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class XenditService
{
    public function createInvoice(array $payload)
    {
        return Http::withToken(config('services.xendit.key'))
            ->post('https://api.xendit.co/v2/invoices', $payload);
    }
}
