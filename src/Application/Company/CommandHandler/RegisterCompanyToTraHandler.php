<?php

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\RegisterCompanyToTraCommand;
use App\Domain\Services\RegistrationCompanyToTraRequest;
use App\Domain\Services\TraIntegrationService;
use Exception;
use Psr\Log\LoggerInterface;

class RegisterCompanyToTraHandler
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

            throw new Exception('An error has been occurred when attempt registration company to TRA',500);
        }

        $this->logger->debug(
            'Registration has been processed successfully',
            [
                'tin' => $command->getTin(),
            ]
        );

    }
}
