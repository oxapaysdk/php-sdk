<?php
namespace OxaPay\SDK\Exceptions;
class HttpException extends SdkException {
    public function __construct(public int $status, string $message, public array $context = []) {
        parent::__construct($message, $status);
    }
}
