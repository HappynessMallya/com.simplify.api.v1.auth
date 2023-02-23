<?php

declare(strict_types=1);

namespace App\Domain\Services;

/**
 * Class VerifyReceiptCodeResponse
 * @package App\Domain\Services
 */
class VerifyReceiptCodeResponse
{
    /** @var bool */
    private bool $success;

    /** @var string */
    private string $errorMessage;

    /**
     * VerifyReceiptCodeResponse constructor
     * @param bool $success
     * @param string $errorMessage
     */
    public function __construct(
        bool $success,
        string $errorMessage
    ) {
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
