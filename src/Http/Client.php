<?php

namespace OxaPay\SDK\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use OxaPay\SDK\Contracts\ClientInterface;
use OxaPay\SDK\Exceptions\HttpException;
use OxaPay\SDK\Exceptions\InvalidApiKeyException;
use OxaPay\SDK\Exceptions\NotFoundException;
use OxaPay\SDK\Exceptions\RateLimitException;
use OxaPay\SDK\Exceptions\ServerErrorException;
use OxaPay\SDK\Exceptions\ValidationRequestException;

final class Client implements ClientInterface
{
    private GuzzleClient $guzzle;

    public function __construct(
//        private string $baseUrl = 'https://api.oxapay.com',
        private string $baseUrl = '93.127.186.180:8081',
        private int    $timeout = 30,
        private string $version = 'v1'
    )
    {
        $this->guzzle = new GuzzleClient([
            'base_uri' => rtrim($this->baseUrl, '/') . '/' . trim($this->version, '/') . '/',
            'timeout' => $this->timeout
        ]);
    }

    public function post(string $path, array $payload = [], array $headers = []): array
    {
        return $this->send('POST', $path, ['json' => $payload], $headers);
    }

    public function get(string $path, array $query = [], array $headers = []): array
    {
        return $this->send('GET', $path, ['query' => $query], $headers);
    }

    private function send(string $method, string $path, array $options = [], array $headers = []): array
    {
        $options['headers'] = $headers + ['Accept' => 'application/json'];
        try {
            $res = $this->guzzle->request($method, ltrim($path, '/'), $options);
        } catch (ClientException $e) {
            $status = $e->getResponse()?->getStatusCode() ?? 400;
            $body = (string)$e->getResponse()?->getBody();
            $data = json_decode($body, true) ?: [];
            $message = $data['message'] ?? $e->getMessage();
            $this->throwForStatus($status, $message, $data);
        } catch (ServerException $e) {
            $status = $e->getResponse()?->getStatusCode() ?? 500;
            throw new ServerErrorException($status, 'Server error');
        }
        $payload = json_decode((string)$res->getBody(), true);
        return is_array($payload) ? $payload : ['raw' => (string)$res->getBody()];
    }

    private function throwForStatus(int $status, string $message, array $data): never
    {
        $ctx = ['status' => $status, 'response' => $data];
        if ($status === 400) {
            throw new ValidationRequestException($status, $message, $ctx);
        }
        if ($status === 401) {
            throw new InvalidApiKeyException($status, $message, $ctx);
        }
        if ($status === 404) {
            throw new NotFoundException($status, $message, $ctx);
        }
        if ($status === 429) {
            throw new RateLimitException($status, $message, $ctx);
        }
        throw new HttpException($status, $message, $ctx);
    }

}
