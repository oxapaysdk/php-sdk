<?php

namespace OxaPay\PHP\Exceptions;

use RuntimeException;

class OxaPayException extends RuntimeException
{
    /**
     * @var array
     */
    protected array $context = [];

    /**
     * Attach full API response context (status, response, etc).
     *
     * @param array $context
     * @return $this
     */
    public function setContext(array $context): static
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get attached context.
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
