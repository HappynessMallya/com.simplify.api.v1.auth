<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\VerifyReceiptCodeCommand;
use App\Domain\Services\VerifyReceiptCodeRequest;
use App\Domain\Services\VerifyReceiptCodeService;
use Psr\Log\LoggerInterface;

/**
 * Class VerifyReceiptCodeHandler
 * @package App\Application\Company\CommandHandler
 */
class VerifyReceiptCodeHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var VerifyReceiptCodeService */
    private VerifyReceiptCodeService $verifyReceiptCode;

    /**
     * VerifyReceiptCodeHandler constructor
     * @param LoggerInterface $logger
     * @param VerifyReceiptCodeService $verifyReceiptCode
     */
    public function __construct(
        LoggerInterface $logger,
        VerifyReceiptCodeService $verifyReceiptCode
    ) {
        $this->logger = $logger;
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

        $response = $this->verifyReceiptCode->onVerifyReceiptCode($request);

        if (!$response->isSuccess()) {
            $this->logger->debug(
                'Failed trying to verufy receipt code',
                [
                    'company_id' => $command->getCompanyId(),
                    'receipt_code' => $command->getReceiptCode(),
                ]
            );
        }
    }
}
