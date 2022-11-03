<?php

namespace App\Domain\Services;

class UploadCertificateToTraRegistrationResponse
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

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
