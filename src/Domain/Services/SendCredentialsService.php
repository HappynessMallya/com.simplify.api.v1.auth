<?php

declare(strict_types=1);

namespace App\Domain\Services;

/**
 * Interface SendCredentialsService
 * @package App\Domain\Services
 */
interface SendCredentialsService
{
    /**
     * @param SendCredentialsRequest $request
     * @return SendCredentialsResponse
     */
    public function onSendCredentials(SendCredentialsRequest $request): SendCredentialsResponse;
}
