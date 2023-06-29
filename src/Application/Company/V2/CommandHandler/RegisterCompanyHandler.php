<?php

declare(strict_types=1);

namespace App\Application\Company\V2\CommandHandler;

use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Repository\CompanyRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;

class RegisterCompanyHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
     * CreateCompanyHandler constructor
     * @param LoggerInterface $logger
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        LoggerInterface $logger,
        CompanyRepository $companyRepository
    ) {
        $this->logger = $logger;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param RegisterCompanyCommand $command
     * @return string|null
     * @throws Exception
     */
    public function handle(RegisterCompanyCommand $command): string
    {
        $companyId = CompanyId::generate();

        $company = Company::create(
            $companyId,
            $command->getName(),
            (int) $command->getTin(),
            $command->getAddress(),
            $command->getEmail(),
            $command->getPhone(),
            new DateTime(),
            CompanyStatus::STATUS_ACTIVE(),
            $command->getSerial(),
            OrganizationId::fromString($command->getOrganizationId())
        );

        $companyRegistered = $this->companyRepository->findOneBy(['tin' => $command->getTin()]);
        if (!empty($companyRegistered)) {
            $this->logger->critical(
                'Company has been registered with TIN number',
                [
                    'tin' => $command->getTin(),
                    'method' => __METHOD__
                ]
            );

            throw new Exception('Company has been registered with TIN number');
        }

        try {
            $this->companyRepository->save($company);

            return $companyId->toString();
        } catch (Exception $exception) {
            $this->logger->critical(
                'Company could not be registered',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception($exception->getMessage());
        }
    }
}
