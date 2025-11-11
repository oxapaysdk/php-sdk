<?php

namespace OxaPay\PHP\Concerns;

trait SandBoxTrait
{
    /** @var bool Default sandbox */
    private bool $sandbox;

    /**
     * Ensure `sandbox` key exists in request data.
     *
     * @param array $data
     * @param bool|null $sandbox
     * @return array
     */
    private function setSandbox(array $data, ?bool $sandbox = null): array
    {
        if (!isset($data['sandbox'])) {
            $data['sandbox'] = $sandbox ?? $this->sandbox;
        }

        return $data;
    }
}
