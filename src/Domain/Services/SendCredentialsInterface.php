<?php

declare(strict_types=1);

namespace App\Domain\Services;

/**
 * Interface SendCredentialsInterface
 * @package App\Domain\Services
 */
interface SendCredentialsInterface
{
    /**
     * @param SendCredentialsRequest $request
     * @return SendCredentialsResponse
     */
    public function onSendCredentials(SendCredentialsRequest $request): SendCredentialsResponse;
}
