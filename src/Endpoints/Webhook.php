<?php

namespace OxaPay\SDK\Endpoints;

use OxaPay\SDK\Exceptions\WebhookSignatureException;
use OxaPay\SDK\Contracts\ClientInterface;

final class Webhook
{
    /**
     * @param string|null $apiKey Raw key or slot; null uses group's default.
     */
    public function __construct(
        private ClientInterface $client,
        private ?string         $apiKey
    ) {}

    /**
     * Get webhook payload.
     *
     * @param bool $verify Validate HMAC if true
     * @return array
     * @throws WebhookSignatureException
     */
    public function getData(bool $verify = true): array
    {
        $data = $_POST;
        if ($verify) {
            $this->verify($data);
        }

        return $data;
    }

    /**
     * Validate HMAC signature (sha512 over raw body).
     *
     * @param array $data
     * @return void
     * @throws WebhookSignatureException
     */
    public function verify(array $data): void
    {
        $hmac = $this->getHeader('HMAC') ?? $this->getHeader('hmac') ?? '';
        if ($hmac === '') {
            throw new WebhookSignatureException('Missing HMAC header.');
        }

        $content = file_get_contents('php://input');

        $calc = hash_hmac('sha512', $content, $this->resolveApiKey($data));

        if (!hash_equals($calc, (string)$hmac)) {
            $exception = new WebhookSignatureException('Invalid HMAC signature.');
            $exception->setContext(['content' => $content, 'hmac' => $hmac, 'new_hmac' => $calc]);
            throw $exception;
        }
    }

    /**
     * Resolve API key from payload type.
     *
     * @param array $data
     * @return string
     */
    protected function resolveApiKey(array $data): string
    {
        $type = (string)($data['type'] ?? '');

        $group = match (true) {
            in_array($type, ['invoice', 'white_label', 'static_address', 'payment_link', 'donation'], true) => 'merchants',
            $type === 'payout' => 'payouts',
            default => 'merchants',
        };

        return $this->apiKey ?? $this->getApiKeyFromConfig($group);
    }

    /**
     * Helper method to get header in a case-insensitive way.
     *
     * @param string $name
     * @return string|null
     */
    protected function getHeader(string $name): ?string
    {
        $headers = getallheaders();
        foreach ($headers as $key => $value) {
            if (strtolower($key) === strtolower($name)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Placeholder method to resolve API key from config or other source.
     *
     * @param string $group
     * @return string
     */
    protected function getApiKeyFromConfig(string $group): string
    {
        $config = [
            'merchants' => 'CLOWVM-EFHYFP-DBM7A3-BVWEGM',
            'payouts' => 'SDUD99-D2U9A0-3FLKEA-RKDURA',
        ];
        return $config[$group] ?? 'default_api_key';
    }
}