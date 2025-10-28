<?php

namespace OxaPay\PHP;

use OxaPay\PHP\Contracts\ClientInterface;
use OxaPay\PHP\Endpoints\Account;
use OxaPay\PHP\Endpoints\Common;
use OxaPay\PHP\Endpoints\Exchange;
use OxaPay\PHP\Endpoints\Payment;
use OxaPay\PHP\Endpoints\Payout;
use OxaPay\PHP\Endpoints\Webhook;
use OxaPay\PHP\Http\Client;

/**
 * @method static Payment payment(string $apiKey)
 * @method static Payout payout(string $apiKey)
 * @method static Exchange exchange(string $apiKey)
 * @method static Common common(string $apiKey)
 * @method static Account account(string $apiKey)
 * @method static Webhook webhook(string $apiKey)
 */

final class OxaPay
{
    private const VERSION = '1.0.0';
    private const BASE_URL = 'https://api.oxapay.com/v1';
    private ClientInterface $client;

    public function __construct(public int $timeout = 20)
    {
        $this->client = new Client(self::BASE_URL, $timeout, self::VERSION);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        static $instance;
        $instance ??= new self();
        return $instance->__call($name, $arguments);
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }
        throw new \BadMethodCallException("Method {$name} does not exist.");
    }

    /**
     * Return a Payment endpoint instance.
     */
    protected function payment(string $apiKey): Payment
    {
        return new Payment(
            $this->client,
            $apiKey
        );
    }

    /**
     * Return an Account endpoint instance.
     */
    protected function account(string $apiKey): Account
    {
        return new Account(
            $this->client,
            $apiKey
        );
    }

    /**
     * Return a Common endpoint instance.
     */
    protected function common(string $apiKey = null): Common
    {
        return new Common(
            $this->client,
            $apiKey
        );
    }

    /**
     * Return an Exchange endpoint instance.
     */
    protected function exchange(string $apiKey): Exchange
    {
        return new Exchange(
            $this->client,
            $apiKey
        );
    }

    /**
     * Return a Payout endpoint instance.
     */
    protected function payout(string $apiKey): Payout
    {
        return new Payout(
            $this->client,
            $apiKey
        );
    }

    /**
     * Return a Webhook endpoint instance.
     */
    protected function webhook(string $apiKey): Webhook
    {
        return new Webhook($apiKey);
    }

}
