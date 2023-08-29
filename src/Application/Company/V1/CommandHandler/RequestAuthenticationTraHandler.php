<?php

declare(strict_types=1);

namespace App\Application\Company\V1\CommandHandler;

use App\Application\Company\V1\Command\RequestAuthenticationTraCommand;
use App\Domain\Services\CompanyStatusOnTraRequest;
use App\Domain\Services\TraIntegrationService;
use Psr\Log\LoggerInterface;

/**
 * Class RequestAuthenticationTraHandler
 * @package App\Application\Company\V1\CommandHandler
 */
class RequestAuthenticationTraHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var TraIntegrationService */
    private TraIntegrationService $traIntegrationService;

    /**
     * @param LoggerInterface $logger
     * @param TraIntegrationService $traIntegrationService
     */
    public function __construct(
        LoggerInterface $logger,
        TraIntegrationService $traIntegrationService
    ) {
        $this->logger = $logger;
        $this->traIntegrationService = $traIntegrationService;
    }

    /**
     * @param RequestAuthenticationTraCommand $command
     * @return void
     */
    public function __invoke(RequestAuthenticationTraCommand $command): void
    {
        $companyStatusOnTraRequest = new CompanyStatusOnTraRequest(
            $command->getCompanyId(),
            $command->getTin(),
            $command->getSerial(),
            $command->getUsername(),
            $command->getPassword()
        );

        $this->logger->debug(
            'Request authentication and status on TRA',
            [
                'companyId' => $command->getCompanyId(),
                'tin' => $command->getTin(),
                'serial' => $command->getSerial(),
                'method' => __METHOD__,
            ]
        );

        $companyStatusOnTraResponse = $this->traIntegrationService->requestCompanyStatusOnTra(
            $companyStatusOnTraRequest
        );

        if (!$companyStatusOnTraResponse->isSuccess()) {
            $this->logger->critical(
                'An error occurred when request status of company on TRA',
                [
                    'company_id' => $companyStatusOnTraRequest->getCompanyId(),
                    'tin' => $companyStatusOnTraRequest->getTin(),
                    'serial' => $companyStatusOnTraRequest->getSerial(),
                    'error_message' => $companyStatusOnTraResponse->getErrorMessage(),
                    'method' => __METHOD__,
                ]
            );
        }

        $this->logger->debug(
            'Authenticated successfully on TRA',
            [
                'companyId' => $command->getCompanyId(),
                'tin' => $command->getTin(),
                'serial' => $command->getSerial(),
                'method' => __METHOD__,
            ]
        );
    }
}
