<?php

namespace OxaPay\PHP\Concerns;

trait CallbackUrlTrait
{
    /** @var string Default callback URL */
    private string $callbackUrl;

    /**
     * Add `callback_url` if missing.
     *
     * @param array $data
     * @param string|null $callbackUrl
     * @return array
     */
    private function setCallbackUrl(array $data, ?string $callbackUrl = null): array
    {
        if (!isset($data['callback_url'])) {
            $data['callback_url'] = $callbackUrl ?? $this->callbackUrl;
        }

        return $data;
    }
}
