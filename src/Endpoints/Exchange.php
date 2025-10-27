<?php

namespace OxaPay\PHP\Endpoints;

use OxaPay\PHP\Contracts\ClientInterface;

final class Exchange
{
    public function __construct(private ClientInterface $client, private ?string $apiKey)
    {
    }

    private function headers(): array
    {
        return ['general_api_key' => $this->apiKey];
    }

    /**
     * Create a exchange request.
     *
     * @param array $data
     * @return array
     */
    public function request(array $data): array
    {
        return $this->client->post('general/swap', $data, $this->headers());
    }

    /**
     * Get exchange history.
     *
     * @param array $filters
     * @return array
     */
    public function history(array $filters = []): array
    {
        return $this->client->get('general/swap', $filters, $this->headers());
    }

    /**
     * Get available exchange pairs.
     *
     * @return array
     */
    public function pairs(): array
    {
        return $this->client->get('general/swap/pairs', [], $this->headers());
    }

    /**
     * Pre-calculate exchange.
     *
     * @param array $data
     * @return array
     */
    public function calculate(array $data): array
    {
        return $this->client->post('general/swap/calculate', $data, $this->headers());
    }

    /**
     * Get exchange rate.
     *
     * @param array $data
     * @return array
     */
    public function rate(array $data): array
    {
        return $this->client->post('general/swap/rate', $data, $this->headers());
    }

}
