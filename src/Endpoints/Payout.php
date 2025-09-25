<?php
namespace OxaPay\SDK\Endpoints;
use OxaPay\SDK\Contracts\ClientInterface;
final class Payout
{
    public function __construct(
        private ClientInterface $client,
        private ?string $apiKey
    ) {}
    private function headers(): array
    {
        return ['payout_api_key' => $this->apiKey];
    }
    public function withdraw(array $data): array
    {
        return $this->client->post('payout/withdraw', $data, $this->headers());
    }
    public function history(array $filters = []): array
    {
        return $this->client->post('payout/history', $filters, $this->headers());
    }
}
