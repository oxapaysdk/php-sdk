<?php

namespace OxaPay\PHP\Endpoints;

use OxaPay\PHP\Contracts\OxaPayClientInterface;

final class Common
{
    public function __construct(protected OxaPayClientInterface $client, protected string $apiKey)
    {
        //
    }

    /**
     * @return array
     */
    protected function headers(): array
    {
        return ['general_api_key' => $this->apiKey];
    }

    /**
     * Get market prices.
     *
     * @return array
     */
    public function prices(): array
    {
        return $this->client->get('common/prices', [], $this->headers());
    }

    /**
     * Get supported cryptocurrencies.
     *
     * @return array
     */
    public function currencies(): array
    {
        return $this->client->get('common/currencies', [], $this->headers());
    }

    /**
     * Get supported fiat currencies.
     *
     * @return array
     */
    public function fiats(): array
    {
        return $this->client->get('common/fiats', [], $this->headers());
    }

    /**
     * Get supported networks.
     *
     * @return array
     */
    public function networks(): array
    {
        return $this->client->get('common/networks', [], $this->headers());
    }

    /**
     * Get system status.
     *
     * @return array
     */
    public function monitor(): array
    {
        return $this->client->get('common/monitor', [], $this->headers());
    }

}
