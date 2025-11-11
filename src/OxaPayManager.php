<?php

namespace OxaPay\PHP;

use OxaPay\PHP\Endpoints\Common;
use OxaPay\PHP\Endpoints\Payout;
use OxaPay\PHP\Services\Webhook;
use OxaPay\PHP\Endpoints\Account;
use OxaPay\PHP\Endpoints\Payment;
use OxaPay\PHP\Http\OxaPayClient;
use OxaPay\PHP\Endpoints\Exchange;
use OxaPay\PHP\Contracts\OxaPayClientInterface;
use OxaPay\PHP\Exceptions\WebhookNotReceivedException;

final class OxaPayManager
{
    private const VERSION  = '1.0.0';
    private const BASE_URL = 'https://api.oxapay.com/v1';
    private OxaPayClientInterface $client;

    public function __construct(public int $timeout = 20, ?OxaPayClientInterface $client = null)
    {
        $this->client = $client ?? new OxaPayClient(self::BASE_URL, $timeout ?: 20, self::VERSION);
    }

    /** Payment APIs.
     *
     * @param string $merchantsApiKey
     * @param string|null $callbackUrl
     * @param bool|null $sandbox
     * @return Payment
     */
    public function payment(string $merchantsApiKey, ?string $callbackUrl = null, ?bool $sandbox = null): Payment
    {
        return new Payment($this->client, $merchantsApiKey, $callbackUrl, $sandbox);
    }

    /** Payout APIs.
     *
     * @param string $payoutApiKey
     * @param string|null $callbackUrl
     * @return Payout
     */
    public function payout(string $payoutApiKey, ?string $callbackUrl = null): Payout
    {
        return new Payout($this->client, $payoutApiKey, $callbackUrl);
    }

    /** Exchange APIs.
     *
     * @param string $generalApiKey
     * @return Exchange
     */
    public function exchange(string $generalApiKey): Exchange
    {
        return new Exchange($this->client, $generalApiKey);
    }

    /** Common APIs.
     *
     * @param string $generalApiKey
     * @return Common
     */
    public function common(string $generalApiKey): Common
    {
        return new Common($this->client, $generalApiKey);
    }

    /** Account APIs.
     *
     * @param string $generalApiKey
     * @return Account
     */
    public function account(string $generalApiKey): Account
    {
        return new Account($this->client, $generalApiKey);
    }

    /**
     * Webhook handler.
     *
     * @param string|null $merchantApiKey
     * @param string|null $payoutApiKey
     * @throws WebhookNotReceivedException
     * @return Webhook
     */
    public function webhook(?string $merchantApiKey = null, ?string $payoutApiKey = null): Webhook
    {
        return new Webhook($merchantApiKey, $payoutApiKey);
    }

}
