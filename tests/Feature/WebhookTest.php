<?php
namespace Tests;

use OxaPay\PHP\Services\Webhook;
use OxaPay\PHP\Exceptions\WebhookSignatureException;

it('verifies INVOICE webhook when merchant key is passed explicitly', function () {
    $payload  = '{"type":"invoice","status":"Paid"}';
    $override = 'custom-merchant-secret';
    $sig      = hash_hmac('sha512', $payload, $override);

    $data = (new Webhook($override, null, $payload, ['HMAC' => $sig]))->getData(true);
    expect($data['status'])->toBe('Paid');
});

it('verifies PAYOUT webhook when key is passed explicitly', function () {
    $payload  = '{"type":"payout","status":"Confirmed"}';
    $override = 'custom-payout-secret';
    $sig      = hash_hmac('sha512', $payload, $override);

    $data = (new Webhook(null, $override, $payload, ['HMAC' => $sig]))->getData(true);
    expect($data['status'])->toBe('Confirmed');
});

it('accepts lowercase hmac header', function () {
    $payload = '{"type":"invoice","status":"Paid"}';
    $secret  = 'merchant-secret';
    $sig     = hash_hmac('sha512', $payload, $secret);

    $data = (new Webhook($secret, null, $payload, ['hmac' => $sig]))->getData(true);
    expect($data['status'])->toBe('Paid');
});

it('throws when HMAC header is missing', function () {
    $payload = '{"type":"invoice","status":"Paid"}';
    (new Webhook('merchant', null, $payload, []))->getData(true);
})->throws(WebhookSignatureException::class, 'Missing HMAC header.');

it('throws on invalid signature', function () {
    $payload = '{"type":"invoice","status":"Paid"}';
    (new Webhook('merchant', null, $payload, ['HMAC' => 'deadbeef']))->getData(true);
})->throws(WebhookSignatureException::class, 'Invalid HMAC signature.');
