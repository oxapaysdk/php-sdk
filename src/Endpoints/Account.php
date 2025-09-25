<?php

namespace OxaPay\SDK\Endpoints;

use OxaPay\SDK\Contracts\ClientInterface;

final class Account
{
    public function __construct(private ClientInterface $client, private ?string $apiKey)
    {
    }

    private function headers(): array
    {
        return ['general_api_key' => $this->apiKey];
    }

    public function balance(string $currency = ''): array
    {
        return $this->client->get('general/account/balance', ['currency' => $currency], $this->headers());
    }

    public function profile(): array
    {
        return $this->client->get('merchant/profile', [], $this->headers());
    }
}
