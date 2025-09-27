<?php

namespace OxaPay\SDK\Endpoints;

use OxaPay\SDK\OxaPay;
use OxaPay\SDK\Exceptions\WebhookSignatureException;

final class Webhook
{
    public function __construct(private OxaPay $oxapay, private ?string $apiKey)
    {
    }

    public function getData(bool $verify = true): array
    {
        $data = request()->all();

        if ($verify) {
            $this->verify($data);
        }

        return $data;
    }

    public function verify(string $payloadJson, string $signature): array
    {
        $data = json_decode($payloadJson, true) ?: [];
        $key = $this->resolveApiKey($data);
        $expected = hash_hmac('sha256', $payloadJson, $key);
        if (!hash_equals($expected, $signature)) {
            throw new WebhookSignatureException('Invalid webhook signature');
        }
        return $data;
    }

    private function resolveApiKey(array $data): string
    {
        $type = (string)($data['type'] ?? '');
        $group = match (true) {
            in_array($type, ['invoice', 'white_label', 'static_address', 'payment_link', 'donation'], true) => 'merchants',
            $type === 'payout' => 'payouts',
            default => 'merchants',
        };
        return $this->oxapay->resolveKey($group, $this->apiKey);
    }
}
