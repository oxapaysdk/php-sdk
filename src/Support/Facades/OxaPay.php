<?php

namespace OxaPay\PHP\Support\Facades;

use OxaPay\PHP\OxaPayManager;
use OxaPay\PHP\Endpoints\Common;
use OxaPay\PHP\Endpoints\Payout;
use OxaPay\PHP\Services\Webhook;
use OxaPay\PHP\Endpoints\Account;
use OxaPay\PHP\Endpoints\Payment;
use OxaPay\PHP\Endpoints\Exchange;

/**
 * @method static Payment payment(string $merchantsApiKey, ?string $callbackUrl = null, ?bool $sandbox = null)
 * @method static Payout payout(string $payoutApiKey, ?string $callbackUrl = null)
 * @method static Exchange exchange(string $generalApiKey)
 * @method static Common common()
 * @method static Account account(string $generalApiKey)
 * @method static Webhook webhook(?string $merchantApiKey = null, ?string $payoutApiKey = null)
 */
class OxaPay
{
    private static OxaPayManager $manager;

    public function __construct()
    {
        self::$manager = new OxaPayManager();
    }

    public static function __callStatic(string $name, array $arguments): Payment|Payout|Exchange|Common|Account|Webhook
    {
        static $instance;
        $instance ??= new self();

        return self::$manager->{$name}(...$arguments);
    }
}
