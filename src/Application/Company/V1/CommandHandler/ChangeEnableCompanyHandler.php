<?php

declare(strict_types=1);

namespace App\Application\Company\V1\CommandHandler;

use App\Application\Company\V1\Command\ChangeEnableCompanyCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChangeEnableCompanyHandler
 * @package App\Application\Company\V1\CommandHandler
 */
class ChangeEnableCompanyHandler
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
     * @param ChangeEnableCompanyCommand $command
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

            throw new Exception(
                'Company could not be found',
                Response::HTTP_NOT_FOUND
            );
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
                'An internal server error has been occurred when trying change enable company',
                [
                    'company_id' => $companyId->toString(),
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'An internal server error has been occurred when trying change enable company',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
