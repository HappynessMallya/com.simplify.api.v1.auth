<?php

namespace App\Domain\Services;

class CreateSubscriptionResponse
{
    private bool $success;

    private string $errorMessage;

    /**
     * @param bool $success
     * @param string $errorMessage
     */
    public function __construct(bool $success, string $errorMessage)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
