<?php
namespace Tests;

use OxaPay\PHP\OxaPay;
use OxaPay\PHP\Contracts\OxaPayClientInterface;

/** @var \PHPUnit\Framework\TestCase $this */
beforeEach(function () {
    /** @var OxaPayClientInterface|\PHPUnit\Framework\MockObject\MockObject $client */
    $client       = $this->createMock(OxaPayClientInterface::class);
    $this->client = $client;
    $this->sdk    = new OxaPay(timeout: 20, client: $client);
});

it('create invoice and returns track_id, payment_url, expired_at, date', function () {
    $payload = ['amount' => 1.23, 'currency' => 'USDT', 'lifetime' => 10];
    $this->client->expects($this->once())
        ->method('post')
        ->with($this->equalTo('payment/invoice'), $this->arrayHasKey('amount'), $this->isType('array'))
        ->willReturn([
            'data' => [
                'track_id'    => '193139644',
                'payment_url' => 'https://pay.oxapay.com/13355044/193139644',
                'expired_at'  => 1755999478,
                'date'        => 1755997678,
            ],
            'message' => 'ok', 'error' => (object)[], 'status' => 200, 'version' => '1.0.0',
        ]);

    $res = $this->sdk->payment('merchant-key')->generateInvoice($payload, callbackUrl: 'https://example.com/cb');

    // unwrap data
    $data = $res['data'] ?? [];

    expect($data)->toBeArray()
        ->and($data)->toHaveKeys(['track_id','payment_url','expired_at','date']);
});
