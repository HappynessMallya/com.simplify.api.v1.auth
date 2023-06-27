<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\ChangeEnableCompanyCommand;
use App\Application\Company\Command\ChangeStatusCompanyCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;

class ChangeEnableCompanyHandler
{
    /** @var LoggerInterface  */
    private LoggerInterface $logger;

    /** @var CompanyRepository  */
    private CompanyRepository $companyRepository;

    /**
     * @param LoggerInterface $logger
     * @param CompanyRepository $companyRepository
     */
    public function __construct(LoggerInterface $logger, CompanyRepository $companyRepository)
    {
        $this->logger = $logger;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param ChangeEnableCompanyCommand $command
     * @return void
     * @throws Exception
     */
    public function handle(ChangeEnableCompanyCommand $command): void
    {
        $companyId = CompanyId::fromString($command->getCompanyId());
        $company = $this->companyRepository->get($companyId);

        if (empty($company)) {
            $this->logger->critical(
                'Company could not be found',
                [
                    'company_id' => $companyId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception('Company could not be found', 404);
        }

        if (!$command->isEnable()) {
            $company->disable();
        } else {
            $company->enable();
        }

        try {
            $this->companyRepository->save($company);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal error has been occurred when trying change enable company',
                [
                    'company_id' => $companyId->toString(),
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception('An internal error has been occurred when trying change enable company');
        }
    }
}
