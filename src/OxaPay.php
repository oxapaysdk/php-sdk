<?php

namespace OxaPay\SDK;

use OxaPay\SDK\Contracts\ClientInterface;
use OxaPay\SDK\Endpoints\Account;
use OxaPay\SDK\Endpoints\Common;
use OxaPay\SDK\Endpoints\Exchange;
use OxaPay\SDK\Endpoints\Payment;
use OxaPay\SDK\Endpoints\Payout;
use OxaPay\SDK\Endpoints\Webhook;
use OxaPay\SDK\Http\Client;

final class OxaPay
{
    public function __construct(
        private ClientInterface $client = new Client(),
        private array $keys = [
            'merchants' => [
                'default' => '',
                'key_2' => ''
            ],
            'payouts' => [
                'default' =>'',
                'key_2' => ''
            ],
            'general' => [
                'default' =>'',
                'key_2' => ''
            ]
        ]
    ) {}

    public function client(): ClientInterface
    {
        return $this->client;
    }

    public function withApiKey(string $group, ?string $rawOrSlot): string
    {
        if (!$rawOrSlot) {
            return (string)($this->keys[$group]['default'] ?? '');
        }
        if (isset($this->keys[$group][$rawOrSlot])) {
            return (string)$this->keys[$group][$rawOrSlot];
        }
        return $rawOrSlot;
    }

    /**
     * Return a Payment endpoint instance.
     */
    public function payment(?string $apiKey = null): Payment
    {
        return new Payment(
            $this->client,
            $this->withApiKey('merchants', $apiKey)
        );
    }

    /**
     * Return an Account endpoint instance.
     */
    public function account(?string $apiKey = null): Account
    {
        return new Account(
            $this->client,
            $this->withApiKey('general', $apiKey)
        );
    }

    /**
     * Return a Common endpoint instance.
     */
    public function common(?string $apiKey = null): Common
    {
        return new Common(
            $this->client,
            $this->withApiKey('general', $apiKey)
        );
    }

    /**
     * Return an Exchange endpoint instance.
     */
    public function exchange(?string $apiKey = null): Exchange
    {
        return new Exchange(
            $this->client,
            $this->withApiKey('general', $apiKey)
        );
    }

    /**
     * Return a Payout endpoint instance.
     */
    public function payout(?string $apiKey = null): Payout
    {
        return new Payout(
            $this->client,
            $this->withApiKey('payouts', $apiKey)
        );
    }

    /**
     * Return a Webhook endpoint instance.
     */
    public function webhook(?string $apiKey = null): Webhook
    {
        return new Webhook($apiKey);
    }

}
