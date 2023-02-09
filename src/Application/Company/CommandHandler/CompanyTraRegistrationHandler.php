<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\CompanyTraRegistrationCommand;
use App\Application\Company\Command\VerifyReceiptCodeCommand;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class CompanyTraRegistrationHandler
 * @package App\Application\Company\CommandHandler
 */
class CompanyTraRegistrationHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var MessageBusInterface */
    private MessageBusInterface $messageBus;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
     * CompanyTraRegistrationHandler constructor
     * @param LoggerInterface $logger
     * @param MessageBusInterface $messageBus
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        LoggerInterface $logger,
        MessageBusInterface $messageBus,
        CompanyRepository $companyRepository
    ) {
        $this->logger = $logger;
        $this->messageBus = $messageBus;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param CompanyTraRegistrationCommand $command
     * @return bool|null
     * @throws Exception
     */
    public function handle(CompanyTraRegistrationCommand $command): ?bool
    {
        $company = $this->companyRepository->findOneBy(['tin' => $command->getTin()]);

        if (empty($company)) {
            $this->logger->critical(
                'Company not found by TIN',
                [
                    'tin' => $command->tin(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception('Company not found by TIN: ' . $command->getTin(), 404);
        }

        $company->updateTraRegistration(json_decode($command->getTraRegistration(), true));

        $isSaved = $this->companyRepository->save($company);

        $dto = new VerifyReceiptCodeCommand(
            $company->companyId()->toString(),
            json_decode($command->getTraRegistration(), true)['RECEIPTCODE']
        );

        $this->messageBus->dispatch($dto);

        return $isSaved;
    }
}
