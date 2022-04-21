<?php

declare(strict_types=1);

namespace App\Domain\Services;

class CompanyAuthenticationTraResponse
{
    /**
     * @var bool
     */
    private bool $success;

    /**
     * @var string
     */
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
