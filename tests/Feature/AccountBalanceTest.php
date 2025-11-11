<?php
namespace Tests;

use OxaPay\PHP\OxaPayManager;
use OxaPay\PHP\Contracts\OxaPayClientInterface;

/** @var \PHPUnit\Framework\TestCase $this */
beforeEach(function () {
    /** @var OxaPayClientInterface|\PHPUnit\Framework\MockObject\MockObject $client */
    $client       = $this->createMock(OxaPayClientInterface::class);
    $this->client = $client;
    $this->sdk    = new OxaPayManager(timeout: 20, client: $client);
});

it('returns account balance (no currency filter) with correct shape', function () {
    $this->client->expects($this->once())
        ->method('get')
        ->with($this->equalTo('general/account/balance'), $this->equalTo(['currency' => '']), $this->isType('array'))
        ->willReturn([
            'data'    => ['USDT' => 10.5, 'BTC' => 0.0022866845, 'ETH' => 0.21],
            'message' => 'ok', 'error' => (object)[], 'status' => 200, 'version' => '1.0.0',
        ]);

    $res = $this->sdk->account('api-key')->balance();

    // unwrap data
    $data = $res['data'] ?? [];

    expect($data)->toBeArray()
        ->and($data)->toHaveKey('USDT')
        ->and($data['USDT'])->toBeNumeric();
});

it('returns only requested currency when currency filter is provided', function () {
    $this->client->expects($this->once())
        ->method('get')
        ->with($this->equalTo('general/account/balance'), $this->equalTo(['currency' => 'BNB']), $this->isType('array'))
        ->willReturn([
            'data'    => ['BNB' => 0.007404826],
            'message' => 'ok',
            'error'   => (object)[],
            'status'  => 200,
            'version' => '1.0.0',
        ]);

    $res = $this->sdk->account('api-key')->balance('BNB');

    // unwrap data
    $data = $res['data'] ?? [];

    expect($data)->toBeArray()
        ->and($data)->toHaveKey('BNB')
        ->and(array_keys($data))->toBe(['BNB']);
});
