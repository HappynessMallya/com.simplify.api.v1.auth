<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\VerifyReceiptCodeCommand;
use App\Domain\Services\VerifyReceiptCodeRequest;
use App\Domain\Services\VerifyReceiptCodeService;

/**
 * Class VerifyReceiptCodeHandler
 * @package App\Application\Company\CommandHandler
 */
class VerifyReceiptCodeHandler
{
    /** @var VerifyReceiptCodeService */
    private VerifyReceiptCodeService $verifyReceiptCode;

    /**
     * VerifyReceiptCodeHandler constructor
     * @param VerifyReceiptCodeService $verifyReceiptCode
     */
    public function __construct(
        VerifyReceiptCodeService $verifyReceiptCode
    ) {
        $this->verifyReceiptCode = $verifyReceiptCode;
    }

    /**
     * @param VerifyReceiptCodeCommand $command
     */
    public function handle(VerifyReceiptCodeCommand $command): void
    {
        $request = new VerifyReceiptCodeRequest(
            $command->getCompanyId(),
            $command->getReceiptCode()
        );

        $this->verifyReceiptCode->onVerifyReceiptCode($request);
    }
}
