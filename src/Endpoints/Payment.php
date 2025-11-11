<?php

namespace OxaPay\PHP\Endpoints;

use OxaPay\PHP\Concerns\SandBoxTrait;
use OxaPay\PHP\Concerns\CallbackUrlTrait;
use OxaPay\PHP\Contracts\OxaPayClientInterface;
use OxaPay\PHP\Exceptions\MissingAddressException;
use OxaPay\PHP\Exceptions\MissingTrackIdException;

final class Payment
{
    use CallbackUrlTrait;
    use SandBoxTrait;

    public function __construct(protected OxaPayClientInterface $client, protected string $apiKey, ?string $callbackUrl = null, ?bool $sandbox = null)
    {
        $this->callbackUrl = $callbackUrl ?: '';
        $this->sandbox     = $sandbox ?? false;
    }

    /**
     * @return array
     */
    private function headers(): array
    {
        return ['merchant_api_key' => $this->apiKey];
    }

    /**
     * Generate an invoice (supports sandbox & callback).
     *
     * @param array $data
     * @param string|null $callbackUrl
     * @param bool|null $sandbox
     * @return array
     */
    public function generateInvoice(array $data, ?string $callbackUrl = null, ?bool $sandbox = null): array
    {
        return $this->client->post('payment/invoice', $this->setCallbackUrl($this->setSandbox($data, $sandbox), $callbackUrl), $this->headers());
    }

    /**
     * Generate white-label payment.
     *
     * @param array $data
     * @param string|null $callbackUrl
     * @return array
     */
    public function generateWhiteLabel(array $data, ?string $callbackUrl = null): array
    {
        return $this->client->post('payment/white-label', $this->setCallbackUrl($data, $callbackUrl), $this->headers());
    }

    /**
     * Generate a static deposit address.
     *
     * @param array $data
     * @param string|null $callbackUrl
     * @return array
     */
    public function generateStaticAddress(array $data, ?string $callbackUrl = null): array
    {
        return $this->client->post('payment/static-address', $this->setCallbackUrl($data, $callbackUrl), $this->headers());
    }

    /**
     * Revoke static addresses.
     *
     * Use-cases:
     * - Provide only $address  → revoke that static address.
     * - Provide only $network  → revoke all static addresses on that network.
     * - Provide both           → revoke the given address on the given network.
     *
     * @param string $address Optional static address to revoke.
     * @param string $network Optional network code (e.g., "TRON", "ERC20").
     * @return void
     */
    public function revokeStaticAddress(string $address = '', string $network = ''): void
    {
        if (!$address && !$network) {
            throw new MissingAddressException('address must be provided!');
        }

        $this->client->post('payment/static-address/revoke', ['address' => $address, 'network' => $network], $this->headers());
    }

    /**
     * List static addresses.
     *
     * @param array $filters
     * @return array
     */
    public function staticAddressList(array $filters = []): array
    {
        return $this->client->get('payment/static-address', $filters, $this->headers());
    }

    /**
     * Get information about a payment by track id.
     *
     * @param int|string $trackId
     * @return array
     */
    public function information(int|string $trackId): array
    {
        if (!$trackId) {
            throw new MissingTrackIdException('Track id must be provided');
        }

        return $this->client->get('payment/' . $trackId, [], $this->headers());
    }

    /**
     * Get payment history.
     *
     * @param array $filters
     * @return array
     */
    public function history(array $filters = []): array
    {
        return $this->client->get('payment', $filters, $this->headers());
    }

    /**
     * Get accepted currencies for payments.
     *
     * @return array
     */
    public function acceptedCurrencies(): array
    {
        return $this->client->get('payment/accepted-currencies', [], $this->headers());
    }

}