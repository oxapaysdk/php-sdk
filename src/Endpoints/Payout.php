<?php

namespace OxaPay\PHP\Endpoints;

use OxaPay\PHP\Contracts\ClientInterface;
use OxaPay\PHP\Exceptions\MissingAddressException;
use OxaPay\PHP\Exceptions\MissingTrackIdException;

final class Payout
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
        return ['payout_api_key' => $this->apiKey];
    }

    private function addCallback(array $data): array
    {
        if (!isset($data['callback_url']) && $this->callbackUrl) {
            $data['callback_url'] = $this->callbackUrl;
        }
        return $data;
    }

    /**
     * Generate a payout request.
     *
     * @param array $data
     * @return array
     */
    public function generate(array $data): array
    {
        if (!($data['address'] ?? '')) {
            throw new MissingAddressException(400, 'address must be provided!');
        }

        return $this->client->post('payout', $this->addCallback($data), $this->headers());
    }

    /**
     * Get payout information by track id.
     *
     * @param int|string $trackId
     * @return array
     */
    public function information(int|string $trackId): array
    {
        if (!$trackId) {
            throw new MissingTrackIdException(400, 'Track id must be provided');
        }

        return $this->client->get('payout/' . $trackId, [], $this->headers());
    }

    /**
     * Get payout history.
     *
     * @param array $filters
     * @return array
     */
    public function history(array $filters = []): array
    {
        return $this->client->get('payout', $filters, $this->headers());
    }

}
