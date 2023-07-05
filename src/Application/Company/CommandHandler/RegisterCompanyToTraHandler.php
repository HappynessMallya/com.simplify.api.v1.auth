<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\RegisterCompanyToTraCommand;
use App\Domain\Services\RegistrationCompanyToTraRequest;
use App\Domain\Services\TraIntegrationService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RegisterCompanyToTraHandler
 * @package App\Application\Company\CommandHandler
 */
class RegisterCompanyToTraHandler
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
     * @param RegisterCompanyToTraCommand $command
     * @return void
     * @throws Exception
     */
    public function __invoke(RegisterCompanyToTraCommand $command): void
    {
        $registrationRequest = new RegistrationCompanyToTraRequest(
            $command->getTin(),
            $command->getCertificateKey(),
            $command->getCertificateSerial(),
            $command->getCertificatePassword()
        );

        $registrationCompanyResponse = $this->traIntegrationService->registrationCompanyToTra($registrationRequest);
        if (!$registrationCompanyResponse->isSuccess()) {
            $this->logger->critical(
                'An error has been occurred when attempt registration company to TRA',
                [
                    'tin' => $command->getTin(),
                    'errorMessage' => $registrationCompanyResponse->getErrorMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'An error has been occurred when attempt registration company to TRA: ' .
                    $registrationCompanyResponse->getErrorMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $this->logger->debug(
            'Registration has been processed successfully',
            [
                'tin' => $command->getTin(),
            ]
        );
    }
}
