<?php

declare(strict_types=1);

namespace App\Domain\Services;

/**
 * Interface VerifyReceiptCodeService
 * @package App\Domain\Services
 */
interface VerifyReceiptCodeService
{
    /**
     * @param VerifyReceiptCodeRequest $request
     * @return VerifyReceiptCodeResponse
     */
    public function onVerifyReceiptCode(VerifyReceiptCodeRequest $request): VerifyReceiptCodeResponse;
}
