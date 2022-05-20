<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\ChangeStatusCompanyCommand;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;

class ChangeStatusCompanyHandler
{
    private LoggerInterface $logger;

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
     * @param ChangeStatusCompanyCommand $command
     * @return void
     * @throws Exception
     */
    public function handle(ChangeStatusCompanyCommand $command): void
    {
        $company = $this->companyRepository->findOneBy([ 'tin' => $command->getTin()]);
        $status = CompanyStatus::byValue($command->getStatus());

        if (empty($company)) {
            $this->logger->critical(
                'Company not found',
                [
                    'tin' => $command->getTin()
                ]
            );
            throw new Exception('Company not found');
        }

        $company->updateCompanyStatus($status);

        $this->companyRepository->save($company);
    }
}
