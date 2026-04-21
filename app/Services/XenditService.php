<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class XenditService
{
    protected function resolvedKey(): string
    {
        $secret = (string) config('services.xendit.secret_key', '');
        if ($secret !== '') {
            return $secret;
        }

        return (string) config('services.xendit.key', '');
    }

    protected function looksLikePublicKey(string $key): bool
    {
        return str_starts_with($key, 'xnd_public_');
    }

    public function createInvoice(array $payload): Response
    {
        return Http::withBasicAuth($this->resolvedKey(), '')
            ->acceptJson()
            ->post('https://api.xendit.co/v2/invoices', $payload);
    }

    public function getInvoice(string $invoiceId): Response
    {
        return Http::withBasicAuth($this->resolvedKey(), '')
            ->acceptJson()
            ->get("https://api.xendit.co/v2/invoices/{$invoiceId}");
    }

    public function isConfigured(): bool
    {
        $key = trim($this->resolvedKey());

        return $key !== '' && ! $this->looksLikePublicKey($key);
    }

    public function configurationIssue(): ?string
    {
        $key = trim($this->resolvedKey());

        if ($key === '') {
            return 'missing';
        }

        if ($this->looksLikePublicKey($key)) {
            return 'public_key_only';
        }

        return null;
    }
}
