<?php

namespace OxaPay\PHP\Services;

use OxaPay\PHP\Exceptions\MissingApiKeyException;
use OxaPay\PHP\Exceptions\WebhookSignatureException;
use OxaPay\PHP\Exceptions\WebhookNotReceivedException;

final class Webhook
{
    private array $data;
    private array $apiKeys;
    private string $rawBody;
    private array $headers;

    /**
     * @param string|null $merchantApiKey
     * @param string|null $payoutApiKey
     * @param string|null $rawBody use in tests
     * @param array|null $headers use in tests
     */
    public function __construct(?string $merchantApiKey = null, ?string $payoutApiKey = null, ?string $rawBody = null, ?array $headers = null)
    {
        $this->rawBody = $rawBody ?? (file_get_contents('php://input') ?: '');

        $data = $this->rawBody !== '' ? json_decode($this->rawBody, true) : null;
        if (!is_array($data)) {
            throw new WebhookNotReceivedException('Webhook is not received!');
        }

        $this->data    = $data;
        $this->headers = $headers ?? (function_exists('getallheaders') ? (getallheaders() ?: []) : []);

        $this->apiKeys = [
            'merchant' => $merchantApiKey,
            'payout'   => $payoutApiKey,
        ];
    }

    /**
     * Set merchant api key
     *
     * @param string $merchantApiKey
     * @return $this
     */
    public function setMerchantApiKey(string $merchantApiKey): Webhook
    {
        $this->apiKeys['merchant'] = $merchantApiKey;

        return $this;
    }

    /**
     * Set payout api key
     *
     * @param string $payoutApiKey
     * @return $this
     */
    public function setPayoutApiKey(string $payoutApiKey): Webhook
    {
        $this->apiKeys['payout'] = $payoutApiKey;

        return $this;
    }

    /**
     * Get webhook payload.
     *
     * @param bool $verify Validate HMAC if true
     * @throws WebhookSignatureException
     * @return array
     */
    public function getData(bool $verify = true): array
    {
        if ($verify) {
            $this->verify();
        }

        return $this->data;
    }

    /**
     * Validate HMAC signature (sha512 over raw body).
     *
     * @throws WebhookSignatureException
     * @return void
     */
    public function verify(): void
    {
        $hmac = ($this->headers['hmac'] ?? null) ?? ($this->headers['HMAC'] ?? null) ?? ($this->headers['Hmac'] ?? null) ?? '';

        if ($hmac === '') {
            throw new WebhookSignatureException('Missing HMAC header.');
        }

        // select key by payload type
        $secret = $this->resolveApiKey($this->data['type'] ?? '');

        // compute and compare
        $calc = hash_hmac('sha512', $this->rawBody, $secret);

        if (!hash_equals($calc, (string)$hmac)) {
            $ex = new WebhookSignatureException('Invalid HMAC signature.');
            $ex->setContext([
                'content'  => $this->rawBody,
                'hmac'     => (string)$hmac,
                'new_hmac' => $calc,
            ]);

            throw $ex;
        }
    }

    /**
     * Resolve API key from payload type.
     *
     * @param string $type
     * @throws MissingApiKeyException
     * @return string
     */
    private function resolveApiKey(string $type): string
    {
        $group = match (true) {
            in_array($type, ['invoice', 'white_label', 'static_address', 'payment_link', 'donation'], true) => 'merchant',
            $type === 'payout'                                                                              => 'payout',

            default => 'merchant',
        };

        $key = $this->apiKeys[$group] ?? null;

        if (!$key) {
            throw new MissingApiKeyException("{$group} API key is not set.");
        }

        return $key;
    }
}
