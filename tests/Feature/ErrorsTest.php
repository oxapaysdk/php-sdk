<?php
namespace Tests;

use OxaPay\PHP\OxaPay;
use OxaPay\PHP\Contracts\OxaPayClientInterface;
use OxaPay\PHP\Exceptions\ValidationRequestException;

/** @var \PHPUnit\Framework\TestCase $this */
beforeEach(function () {
    /** @var OxaPayClientInterface|\PHPUnit\Framework\MockObject\MockObject $client */
    $client       = $this->createMock(OxaPayClientInterface::class);
    $this->client = $client;
    $this->sdk    = new OxaPay(timeout: 20, client: $client);
});

it('maps 400 to ValidationRequestException with correct context', function () {
    $e = (new ValidationRequestException('bad request'))->setContext([
        'response' => [
            'status' => 400,
            'error'  => ['key' => 'lifetime', 'message' => 'The lifetime field must be an integer.'],
        ],
    ]);
    $this->client->method('post')->willThrowException($e);

    try {
        $this->sdk->payment('k')->generateInvoice(['amount' => 1.23,'currency' => 'USDT','lifetime' => 1.23]);
        expect()->fail('Exception was not thrown.');
    } catch (ValidationRequestException $ex) {
        $ctx = method_exists($ex, 'getContext') ? $ex->getContext() : [];
        expect(($ctx['response']['error']['key'] ?? null))->toBe('lifetime');
        expect(($ctx['response']['status'] ?? null))->toBe(400);
    }
});
