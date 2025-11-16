<?php

namespace OxaPay\PHP\Endpoints;

use OxaPay\PHP\Contracts\OxaPayClientInterface;

final class Common
{
    public function __construct(protected OxaPayClientInterface $client)
    {
        //
    }

    /**
     * Get market prices.
     *
     * @return array
     */
    public function prices(): array
    {
        return $this->client->get('common/prices');
    }

    /**
     * Get supported cryptocurrencies.
     *
     * @return array
     */
    public function currencies(): array
    {
        return $this->client->get('common/currencies');
    }

    /**
     * Get supported fiat currencies.
     *
     * @return array
     */
    public function fiats(): array
    {
        return $this->client->get('common/fiats');
    }

    /**
     * Get supported networks.
     *
     * @return array
     */
    public function networks(): array
    {
        return $this->client->get('common/networks');
    }

    /**
     * Get system status.
     *
     * @return array
     */
    public function monitor(): array
    {
        return $this->client->get('common/monitor');
    }

}
