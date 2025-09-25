<?php

namespace OxaPay\SDK\Contracts;

interface ClientInterface {
    public function post(string $path, array $payload = [], array $headers = []): array;
    public function get(string $path, array $query = [], array $headers = []): array;
}
