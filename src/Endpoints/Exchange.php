<?php
namespace OxaPay\SDK\Endpoints;
use OxaPay\SDK\Contracts\ClientInterface;
final class Exchange
{
    public function __construct(private ClientInterface $client, private ?string $apiKey) {}
    private function headers(): array
    {
        return $this->apiKey ? ['Authorization' => 'Bearer '.$this->apiKey] : [];
    }
    public function rates(string $fiat = 'USD'): array
    {
        return $this->client->get('merchant/rates', ['fiat' => $fiat], $this->headers());
    }
}
