<?php

namespace OxaPay\PHP\Contracts;

use OxaPay\PHP\Exceptions\HttpException;
use OxaPay\PHP\Exceptions\NotFoundException;
use OxaPay\PHP\Exceptions\RateLimitException;
use OxaPay\PHP\Exceptions\ServerErrorException;
use OxaPay\PHP\Exceptions\InvalidApiKeyException;
use OxaPay\PHP\Exceptions\ValidationRequestException;
use OxaPay\PHP\Exceptions\ServiceUnavailableException;

interface OxaPayClientInterface {

    /**
     * Send POST request.
     *
     * @param string $path
     * @param array $payload
     * @param array $headers
     * @throws HttpException|ValidationRequestException|InvalidApiKeyException|NotFoundException|ServerErrorException|ServiceUnavailableException|RateLimitException
     * @return array
     */
    public function post(string $path, array $payload = [], array $headers = []): array;

    /**
     * Send GET request.
     *
     * @param string $path
     * @param array $query
     * @param array $headers
     * @throws HttpException|ValidationRequestException|InvalidApiKeyException|NotFoundException|ServerErrorException|ServiceUnavailableException|RateLimitException
     * @return array
     */
    public function get(string $path, array $query = [], array $headers = []): array;
}
