<?php

namespace OxaPay\PHP\Http;

use OxaPay\PHP\Exceptions\HttpException;
use OxaPay\PHP\Exceptions\NotFoundException;
use Symfony\Component\HttpClient\HttpClient;
use OxaPay\PHP\Exceptions\RateLimitException;
use OxaPay\PHP\Contracts\OxaPayClientInterface;
use OxaPay\PHP\Exceptions\ServerErrorException;
use OxaPay\PHP\Exceptions\InvalidApiKeyException;
use OxaPay\PHP\Exceptions\ValidationRequestException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use OxaPay\PHP\Exceptions\ServiceUnavailableException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class OxaPayClient implements OxaPayClientInterface
{
    protected HttpClientInterface $client;

    public function __construct(
        protected string $baseUrl,
        protected int    $timeout,
        protected string $version,
        ?HttpClientInterface $httpClient = null,
    )
    {
        $this->client = $httpClient ?? HttpClient::create([
            'verify_peer' => true,
        ]);
    }

    /**
     * Send POST request.
     *
     * @param string $path
     * @param array $payload
     * @param array $headers
     * @throws HttpException|ValidationRequestException|InvalidApiKeyException|NotFoundException|ServerErrorException|ServiceUnavailableException|RateLimitException
     * @return array
     */
    public function post(string $path, array $payload = [], array $headers = []): array
    {
        return $this->handleRequest('POST', $path, $payload, $headers);
    }

    /**
     * Send GET request.
     *
     * @param string $path
     * @param array $query
     * @param array $headers
     * @throws HttpException|ValidationRequestException|InvalidApiKeyException|NotFoundException|ServerErrorException|ServiceUnavailableException|RateLimitException
     * @return array
     */
    public function get(string $path, array $query = [], array $headers = []): array
    {
        return $this->handleRequest('GET', $path, $query, $headers);
    }

    /**
     * Send request and handle exception.
     *
     * @param string $method
     * @param string $path
     * @param array $data
     * @param array $headers
     * @throws HttpException|ValidationRequestException|InvalidApiKeyException|NotFoundException|ServerErrorException|ServiceUnavailableException|RateLimitException
     * @return array
     */
    private function handleRequest(string $method, string $path, array $data, array $headers): array
    {
        try {
            // build request options
            $options = [
                'headers' => $this->baseHeaders($headers),
                'timeout' => $this->timeout,
            ];

            // attach body/query based on method
            if ($method === 'POST') {
                $options['json'] = $data;   // send JSON body
            } else {
                $options['query'] = $data;  // send query params
            }

            $res = $this->client->request($method, $this->endpoint($path), $options);

            // read status & body safely (no exceptions for 4xx/5xx)
            $status = $res->getStatusCode();
            $body   = $res->getContent(false);
            $json   = json_decode($body, true) ?: [];

        } catch (TransportExceptionInterface $e) {
            // wrap network/DNS/TLS timeouts etc.
            throw $this->getHttpException($e, 'Network error');
        } catch (\Throwable $e) {
            // any other unexpected error
            throw $this->getHttpException($e, 'HTTP client error');
        }

        if ($status >= 400) {
            throw $this->getSdkException($status, $json, $body);
        }

        return $json['data'] ?? [];
    }

    /**
     * Build absolute endpoint URL
     *
     * @param string $path
     * @return string
     */
    private function endpoint(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Merge default headers
     *
     * @param array $headers
     * @return array
     */
    private function baseHeaders(array $headers = []): array
    {
        return array_merge([
            'Origin' => 'oxa-php-sdk-v-' . $this->version,
        ], $headers);
    }

    /**
     * Map HTTP status/body to SDK exceptions.
     *
     * @param int $status // HTTP status code
     * @param array $json // parsed body (best-effort)
     * @param string $body // raw response body
     * @return HttpException|InvalidApiKeyException|NotFoundException|RateLimitException|ServerErrorException|ServiceUnavailableException|ValidationRequestException
     */
    private function getSdkException(int $status, array $json, string $body): HttpException|ServerErrorException|ValidationRequestException|InvalidApiKeyException|ServiceUnavailableException|NotFoundException|RateLimitException
    {
        $base = (string)(($json['message'] ?? $body) ?: 'HTTP error');
        $errM = (string)($json['error']['message'] ?? '');
        $msg  = rtrim($base) . ($errM !== '' ? ' ' . $errM : '');

        $ex = match ($status) {
            400     => new ValidationRequestException($msg),
            401     => new InvalidApiKeyException($msg),
            404     => new NotFoundException($msg),
            429     => new RateLimitException($msg),
            500     => new ServerErrorException($msg),
            503     => new ServiceUnavailableException($msg),
            default => new HttpException($msg),
        };

        $ex->setContext([
            'status'   => $status,
            'response' => $json,
        ]);

        return $ex;
    }

    /**
     * Wrap non-SDK Throwable into HttpException with minimal context.
     *
     * @param \Throwable $e
     * @param string $fallback
     * @return HttpException
     */
    private function getHttpException(\Throwable $e, string $fallback): HttpException
    {
        $ex = new HttpException($e->getMessage() ?: $fallback, previous: $e);

        $ex->setContext([
            'previous' => get_class($e),
            'message'  => $e->getMessage(),
        ]);

        return $ex;
    }
}
