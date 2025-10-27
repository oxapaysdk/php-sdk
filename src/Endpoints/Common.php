<?php

namespace OxaPay\PHP\Endpoints;

use OxaPay\PHP\Contracts\ClientInterface;

final class Common
{
    public function __construct(private ClientInterface $client, private ?string $apiKey)
    {
    }

    private function headers(): array
    {
        return ['general_api_key' => $this->apiKey];
    }

    public function prices(): array
    {
        return $this->client->get('common/prices', [], $this->headers());
    }

    public function currencies(): array
    {
        return $this->client->get('common/currencies', [], $this->headers());
    }

    public function fiats(): array
    {
        return $this->client->get('common/fiats', [], $this->headers());
    }

    public function networks(): array
    {
        return $this->client->get('common/networks', [], $this->headers());
    }

    public function monitor(): array
    {
        return $this->client->get('common/monitor', [], $this->headers());
    }

}
