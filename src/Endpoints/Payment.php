<?php

namespace OxaPay\PHP\Endpoints;

use OxaPay\PHP\Contracts\ClientInterface;
use OxaPay\PHP\Exceptions\MissingAddressException;
use OxaPay\PHP\Exceptions\MissingTrackIdException;

final class Payment
{
    public function __construct(
        private ClientInterface $client,
        private ?string         $apiKey
    ) {
        $this->callbackUrl = '';
    }

    private $callbackUrl;

    private function headers(): array
    {
        return ['merchant_api_key' => $this->apiKey];
    }

    private function addCallback(array $data): array
    {
        if (!isset($data['callback_url']) && $this->callbackUrl) {
            $data['callback_url'] = $this->callbackUrl;
        }
        return $data;
    }

    public function generateInvoice(array $data): array
    {
        return $this->client->post('payment/invoice', $this->addCallback($data), $this->headers());
    }

    public function generateWhiteLabel(array $data): array
    {
        return $this->client->post('payment/white-label', $this->addCallback($data), $this->headers());
    }

    public function generateStaticAddress(array $data): array
    {
        return $this->client->post('payment/static-address', $this->addCallback($data), $this->headers());
    }

    public function revokeStaticAddress(string $address = '', string $network = ''): array
    {
        if (!$address && !$network) {
            throw new MissingAddressException(400, 'address must be provided!');
        }

        return $this->client->post('payment/static-address/revoke', ['address' => $address, 'network' => $network], $this->headers());
    }

    public function staticAddressList(array $filters = []): array
    {
        return $this->client->get('payment/static-address', $filters, $this->headers());
    }

    public function information(int|string $trackId): array
    {
        if (!$trackId) {
            throw new MissingTrackIdException(400, 'Track id must be provided');
        }

        return $this->client->get('payment/' . $trackId, [], $this->headers());
    }

    public function history(array $filters = []): array
    {
        return $this->client->get('payment', $filters, $this->headers());
    }

    public function acceptedCurrencies(): array
    {
        return $this->client->get('payment/accepted-currencies', [], $this->headers());
    }

}
