<?php

namespace OxaPay\PHP\Endpoints;

use OxaPay\PHP\Contracts\OxaPayClientInterface;

final class Account
{
    public function __construct(protected OxaPayClientInterface $client, protected string $apiKey)
    {
        //
    }

    /**
     * @return array
     */
    private function headers(): array
    {
        return ['general_api_key' => $this->apiKey];
    }

    /**
     * Get account balance.
     *
     * @param string $currency
     * @return array
     */
    public function balance(string $currency = ''): array
    {
        return $this->client->get('general/account/balance', ['currency' => $currency], $this->headers());
    }

}
