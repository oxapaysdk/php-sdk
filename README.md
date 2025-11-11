# OxaPay PHP SDK

Official PHP SDK for [OxaPay](https://oxapay.com) — accept crypto payments, exchanges, and payouts.

> **PHP:** 8.0+  
> **Docs:** https://docs.oxapay.com

## Installation

```bash
composer require oxapay/php-sdk
```

After installing, if you updated autoloaded classes, run:

```bash
composer dump-autoload -o
```

## Quick start

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use OxaPay\PHP\OxaPayManager;
use OxaPay\PHP\Support\Facades\OxaPay;

// via static method
$oxapay = new OxaPayManager(timeout: 10);
$res = $oxapay->payment("XXXXXX-XXXXXX-XXXXXX-XXXXXX")
              ->createInvoice([
                  'amount' => 10.5,
                  'currency' => 'USDT'
              ]);

// via facade
$res = OxaPay::payment("XXXXXX-XXXXXX-XXXXXX-XXXXXX")
               ->createInvoice([
                   'amount' => 10.5,
                   'currency' => 'USDT'
               ]);

print_r($res);
```

## Handling Webhooks (Payments & Payouts)

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use OxaPay\PHP\Support\Facades\OxaPay;
use OxaPay\PHP\Exceptions\WebhookSignatureException;


// use for merchant webhook endpoint
try {
    $data = OxaPay::webhook(merchantApiKey: "XXXXXX-XXXXXX-XXXXXX-XXXXXX")->getData();
    // ...
} catch (WebhookSignatureException $e) {
    // ...
}

// use for payout webhook endpoint
try {
    $data = OxaPay::webhook(payoutApiKey: "XXXXXX-XXXXXX-XXXXXX-XXXXXX")->getData();
    // ...
} catch (WebhookSignatureException $e) {
    // ...
}

// Use when your endpoint is used for both webhook merchant and payout
try {
    $data = OxaPay::webhook(merchantApiKey: "XXXXXX-XXXXXX-XXXXXX-XXXXXX", payoutApiKey: "XXXXXX-XXXXXX-XXXXXX-XXXXXX")->getData();
    // ...
} catch (WebhookSignatureException $e) {
    // ...
}

// or you can get data without verify HMAC
$data = OxaPay::webhook()->getData(false);
```

---
## Available methods
### 🔹payment
- `generateInvoice` – Create invoice & get payment URL. [More details](https://docs.oxapay.com/api-reference/payment/generate-invoice)
- `generateWhiteLabel` – White-label payment. [More details](https://docs.oxapay.com/api-reference/payment/generate-white-label)
- `generateStaticAddress` – Create static deposit address. [More details](https://docs.oxapay.com/api-reference/payment/generate-static-address)
- `revokeStaticAddress` – Revoke static address. [More details](https://docs.oxapay.com/api-reference/payment/revoking-static-address)
- `staticAddressList` – List static addresses. [More details](https://docs.oxapay.com/api-reference/payment/static-address-list)
- `information` – Single payment information. [More details](https://docs.oxapay.com/api-reference/payment/payment-information)
- `history` – Payment history list. [More details](https://docs.oxapay.com/api-reference/payment/payment-history)
- `acceptedCurrencies` – Accepted currencies. [More details](https://docs.oxapay.com/api-reference/payment/accepted-currencies)

### 🔹account
- `balance` – Account balance. [More details](https://docs.oxapay.com/api-reference/common/account-balance)

### 🔹payout
- `generate` – Request payout. [More details](https://docs.oxapay.com/api-reference/payout/generate-payout)
- `information` – Single payout information. [More details](https://docs.oxapay.com/api-reference/payout/payout-information)
- `history` – Payout history list. [More details](https://docs.oxapay.com/api-reference/payout/payout-history)

### 🔹swap
- `swapRequest` – Swap request. [More details](https://docs.oxapay.com/api-reference/swap/swap-request)
- `swapHistory` – Swap history. [More details](https://docs.oxapay.com/api-reference/swap/swap-history)
- `swapPairs` – Swap pairs. [More details](https://docs.oxapay.com/api-reference/swap/swap-pairs)
- `swapCalculate` – Swap pre-calc. [More details](https://docs.oxapay.com/api-reference/swap/swap-calculate)
- `swapRate` – Swap Quote rate. [More details](https://docs.oxapay.com/api-reference/swap/swap-rate)

### 🔹common
- `prices` – Market prices. [More details](https://docs.oxapay.com/api-reference/common/prices)
- `currencies` – Supported crypto. [More details](https://docs.oxapay.com/api-reference/common/supported-currencies)
- `fiats` – Supported fiats. [More details](https://docs.oxapay.com/api-reference/common/supported-fiat-currencies)
- `networks` – Supported networks. [More details](https://docs.oxapay.com/api-reference/common/supported-networks)
- `monitor` – System status. [More details](https://docs.oxapay.com/api-reference/common/system-status)

### 🔹webhook
- `verify` – Validates `HMAC` header (sha512 of raw body).
- `getData` – Validates `HMAC` header and return webhook data. [More details](https://docs.oxapay.com/webhook)


---
## Exceptions
All SDK exceptions extend `OxaPay\PHP\Exceptions\OxaPayException`:
- `ValidationRequestException` (HTTP 400)
- `InvalidApiKeyException` (HTTP 401)
- `NotFoundException` (HTTP 404)
- `RateLimitException` (HTTP 429)
- `ServerErrorException` (HTTP 500)
- `ServiceUnavailableException` (HTTP 503)
- `HttpException` (network/unknown)
- `MissingApiKeyException` (missing api key)
- `MissingTrackIdException` (missing track id)
- `MissingAddressException` (missing address)
- `WebhookSignatureException` (bad/missing HMAC)
- `WebhookNotReceivedException` (webhook request was not received)



### Security Notes
- Verify webhook HMAC before use input data.
- Whitelist OxaPay IPs on your firewall (ask support).
- Use HTTPS everywhere.
- Store keys in `.env`, not code.
- Rotate keys regularly.
---


## Testing (safe & offline)

This package uses **Pest**, **PHPUnit**, and **Orchestra Testbench** for testing.  
Dependencies are already listed under `require-dev` in `composer.json`.

Run tests with composer:

```bash
composer test
```

Run tests with pest:

```bash
vendor/bin/pest
```

---
## Compatibility

- PHP +8.x


## Security

If you discover a security vulnerability, please email [security@oxapay.com](mailto:security@oxapay.com).  
Do not disclose publicly until it has been fixed.

## Contributing

Pull requests are welcome. For major changes, open an issue first.  
Run coding standards & static analysis before PR:

```bash
composer cs-fix
composer phpstan
composer test
```


## License

Apache-2.0 — see [LICENSE](LICENSE).

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

---
OxaPay Made with ♥ for PHP.
