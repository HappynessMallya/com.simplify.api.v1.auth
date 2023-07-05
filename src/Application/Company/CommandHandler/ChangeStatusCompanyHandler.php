<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\ChangeStatusCompanyCommand;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChangeStatusCompanyHandler
 * @package App\Application\Company\CommandHandler
 */
class ChangeStatusCompanyHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
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
     * @param ChangeStatusCompanyCommand $command
     * @return void
     * @throws Exception
     */
    public function handle(ChangeStatusCompanyCommand $command): void
    {
        $company = $this->companyRepository->findOneBy(
            [
                'tin' => $command->getTin(),
            ]
        );

        $status = CompanyStatus::byValue($command->getStatus());

        if (empty($company)) {
            $this->logger->critical(
                'Company could not be found',
                [
                    'tin' => $command->getTin(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        if (CompanyStatus::byValue($company->companyStatus())->sameValueAs($status)) {
            $this->logger->critical(
                'The company already has the same status',
                [
                    'company_id' => $company->companyId()->toString(),
                    'current_status' => $company->companyStatus(),
                    'new_status' => $status->getValue(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'The company already has the same status',
                Response::HTTP_BAD_REQUEST
            );
        }

        $company->updateCompanyStatus($status);

        $this->companyRepository->save($company);
    }
}
