<?php

declare(strict_types=1);

namespace App\Application\Company\V1\CommandHandler;

use App\Application\Company\Command\BatchRequestTokenToTraCommand;
use App\Application\Company\V1\Command\BatchRequestAuthenticationTraCommand;
use App\Domain\Services\BatchRequestTokenByCompanyToTra;
use App\Domain\Services\TraIntegrationService;
use Psr\Log\LoggerInterface;

/**
 * Class RequestAuthenticationTraHandler
 * @package App\Application\Company\V1\CommandHandler
 */
class BatchRequestAuthenticationTraHandler
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
     * @param BatchRequestTokenToTraCommand $command
     * @return void
     */
    public function __invoke(BatchRequestAuthenticationTraCommand $command): void
    {
        $batchCompaniesRequestToken = new BatchRequestTokenByCompanyToTra(
            $command->getCompanies()
        );

        $response = $this->traIntegrationService->batchRequestTokenByCompanyToTra(
            $batchCompaniesRequestToken
        );

        if (!$response->isSuccess()) {
            $this->logger->critical(
                'An error occurred when request status of company on TRA',
                [
                    'companies' => $batchCompaniesRequestToken->getCompanies(),
                    'error_message' => $response->getErrorMessage(),
                    'method' => __METHOD__,
                ]
            );
        }
    }
}
