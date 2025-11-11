<?php

namespace OxaPay\PHP\Endpoints;

use OxaPay\PHP\Contracts\OxaPayClientInterface;

final class Exchange
{
    public function __construct(protected OxaPayClientInterface $client, protected string $apiKey)
    {
        //
    }

    private function headers(): array
    {
        return ['general_api_key' => $this->apiKey];
    }

    /**
     * Create a swap request.
     *
     * @param array $data
     * @return array
     */
    public function swapRequest(array $data): array
    {
        return $this->client->post('general/swap', $data, $this->headers());
    }

    /**
     * Get swap history.
     *
     * @param array $filters
     * @return array
     */
    public function swapHistory(array $filters = []): array
    {
        return $this->client->get('general/swap', $filters, $this->headers());
    }

    /**
     * Get available swap pairs.
     *
     * @return array
     */
    public function swapPairs(): array
    {
        return $this->client->get('general/swap/pairs', [], $this->headers());
    }

    /**
     * Pre-calculate swap.
     *
     * @param array $data
     * @return array
     */
    public function swapCalculate(array $data): array
    {
        return $this->client->post('general/swap/calculate', $data, $this->headers());
    }

    /**
     * Get swap rate.
     *
     * @param array $data
     * @return array
     */
    public function swapRate(array $data): array
    {
        return $this->client->post('general/swap/rate', $data, $this->headers());
    }

}
