<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\RequestAuthenticationTraCommand;
use App\Domain\Services\CompanyStatusOnTraRequest;
use App\Domain\Services\TraIntegrationService;
use Psr\Log\LoggerInterface;

/**
 * Class RequestAuthenticationTraHandler
 * @package App\Application\Company\CommandHandler
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
        $startTimeHandler = microtime(true);
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

        $start = microtime(true);
        $companyStatusOnTraResponse = $this->traIntegrationService->requestCompanyStatusOnTra(
            $companyStatusOnTraRequest
        );
        $end = microtime(true);

        $this->logger->debug(
            'Time duration request company status on TRA',
            [
                'time' => $end - $start,
                'tin' => $command->getTin(),
                'company_id' => $command->getCompanyId(),
            ]
        );

        if (!$companyStatusOnTraResponse->isSuccess()) {
            $this->logger->critical(
                'An error occurred when request status of company on TRA',
                [
                    'company_id' => $companyStatusOnTraRequest->getCompanyId(),
                    'tin' => $companyStatusOnTraRequest->getTin(),
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
                'method' => __METHOD__,
            ]
        );

        $endTimeHandler = microtime(true);
        $this->logger->debug(
            'Time duration of Request Authentication TRA handler',
            [
                'time' => $endTimeHandler - $startTimeHandler,
                'method' => __METHOD__,
            ]
        );
    }
}
