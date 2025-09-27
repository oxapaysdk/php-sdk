<?php

namespace OxaPay\SDK\Endpoints;

use OxaPay\SDK\Contracts\ClientInterface;
use OxaPay\SDK\Exceptions\MissingAddressException;
use OxaPay\SDK\Exceptions\MissingTrackIdException;

final class Payout
{
    public function __construct(
        private ClientInterface $client,
        private ?string         $apiKey,
        private string          $callbackUrl = ''
    ) {}

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
            throw new MissingAddressException(400,'address must be provided!');
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
