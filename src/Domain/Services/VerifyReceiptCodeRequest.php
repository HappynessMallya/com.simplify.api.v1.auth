<?php

declare(strict_types=1);

namespace App\Domain\Services;

/**
 * Class VerifyReceiptCodeRequest
 * @package App\Domain\Services
 */
class VerifyReceiptCodeRequest
{
    /** @var string */
    private string $companyId;

    /** @var string */
    private string $receiptCode;

    /**
     * VerifyReceiptCodeRequest constructor
     * @param string $companyId
     * @param string $receiptCode
     */
    public function __construct(
        string $companyId,
        string $receiptCode
    ) {
        $this->companyId = $companyId;
        $this->receiptCode = $receiptCode;
    }

    /**
     * @return string
     */
    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getReceiptCode(): string
    {
        return $this->receiptCode;
    }
}
