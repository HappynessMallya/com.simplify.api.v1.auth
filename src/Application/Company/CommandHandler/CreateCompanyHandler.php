<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\CreateCompanyCommand;
use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Repository\CompanyRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class CreateCompanyHandler
 * @package App\Application\Company\CommandHandler
 */
class CreateCompanyHandler
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
     * @param CreateCompanyCommand $command
     * @return string|null
     * @throws Exception
     */
    public function handle(CreateCompanyCommand $command): ?string
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
            null
        );

        try {
            $isSaved = $this->companyRepository->save($company);
        } catch (Exception $exception) {
            $this->logger->critical(
                $exception->getMessage(),
                [
                    'method' => __METHOD__,
                ]
            );

            throw new Exception($exception->getMessage());
        }

        if ($isSaved) {
            return $companyId->toString();
        }

        return null;
    }
}
