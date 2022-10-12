<?php

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\RequestAuthenticationTraCommand;
use App\Domain\Services\CompanyStatusOnTraRequest;
use App\Domain\Services\TraIntegrationService;
use Psr\Log\LoggerInterface;

class RequestAuthenticationTraHandler
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var TraIntegrationService
     */
    private TraIntegrationService $traIntegrationService;

    /**
     * @param LoggerInterface $logger
     * @param TraIntegrationService $traIntegrationService
     */
    public function __construct(LoggerInterface $logger, TraIntegrationService $traIntegrationService)
    {
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
            $command->getUsername(),
            $command->getPassword()
        );

        $this->logger->debug(
            'Request authentication and status on TRA',
            [
                'companyId' => $command->getCompanyId(),
                'tin' => $command->getTin(),
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
                    'error_message' => $companyStatusOnTraResponse->getErrorMessage(),
                ]
            );
        }

        $this->logger->debug(
            'Authenticated successfully on TRA',
            [
                'companyId' => $command->getCompanyId(),
                'tin' => $command->getTin(),
                'method' => __METHOD__,
            ]
        );
    }
}
