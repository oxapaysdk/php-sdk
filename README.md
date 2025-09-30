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

// via custom client
use OxaPay\SDK\OxaPay;
use OxaPay\SDK\Http\Client;
$keys = [
    'merchants' => [
        'default' => 'your_merchant_key'
    ],
    'payouts' => [
        'default' => 'your_payout_key'
    ],
    'general' => [
        'default' => 'your_general_key'
    ]
];
$client = new Client([
    'baseUrl' => 'https://api.oxapay.com',
    'timeout' => 30,
]);
$oxapay = new OxaPay(client: $client, keys: $keys);

// Example: create invoice
$res = $oxapay->payment()->createInvoice([
    'amount' => 10.5,
    'currency' => 'USDT'
]);


// via default client
$oxapay = new OxaPay();

// use raw key
// Example: create invoice
$res = $oxapay->payment("XXXXXX-XXXXXX-XXXXXX-XXXXXX")->createInvoice([
    'amount' => 10.5,
    'currency' => 'USDT'
]);

print_r($res);
```

## Handling Webhooks (Payments & Payouts)

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use OxaPay\SDK\OxaPay;
use OxaPay\SDK\Exceptions\WebhookSignatureException;

$oxapay = new OxaPay();
try {
    $data = $oxapay->webhook("XXXXXX-XXXXXX-XXXXXX-XXXXXX")->getData();
    // ...
} catch (WebhookSignatureException $e) {
    // ...
}
```

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

### 🔹exchange
- `request` – Exchange request. [More details](https://docs.oxapay.com/api-reference/swap/swap-request)
- `history` – Exchange history. [More details](https://docs.oxapay.com/api-reference/swap/swap-history)
- `pairs` – Exchange pairs. [More details](https://docs.oxapay.com/api-reference/swap/swap-pairs)
- `calculate` – Pre-calc. [More details](https://docs.oxapay.com/api-reference/swap/swap-calculate)
- `rate` – Quote rate. [More details](https://docs.oxapay.com/api-reference/swap/swap-rate)

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
All SDK exceptions extend a common base (e.g. `OxaPay\SDK\Exceptions\OxaPayException`):

- `ValidationRequestException` — HTTP 400
- `InvalidApiKeyException` — HTTP 401
- `NotFoundException` — HTTP 404
- `RateLimitException` — HTTP 429
- `ServerErrorException` — HTTP 500
- `ServiceUnavailableException` — HTTP 503
- `HttpException` — network/unknown
- `WebhookSignatureException` — missing/invalid HMAC
- `MissingApiKeyException` (missing api key)
- `MissingTrackIdException` (missing track id)
- `MissingAddressException` (missing address)
---

### Security Notes
- Verify webhook HMAC before use input data.
- Whitelist OxaPay IPs on your firewall (ask support).
- Use HTTPS everywhere.
- Store keys in `.env`, not code.
- Rotate keys regularly.
---

## Security
If you discover a security vulnerability, please email [security@oxapay.com](mailto:security@oxapay.com).  
Do not disclose publicly until it has been fixed.
---

## License & Changelog
- License: Apache-2.0 (or the license defined in your repository)  
- See `CHANGELOG.md` for version history.
