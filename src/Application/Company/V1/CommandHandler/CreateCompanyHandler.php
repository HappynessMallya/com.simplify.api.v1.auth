<?php

declare(strict_types=1);

namespace App\Application\Company\V1\CommandHandler;

use App\Application\Company\V1\Command\CreateCompanyCommand;
use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Repository\CompanyRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreateCompanyHandler
 * @package App\Application\Company\V1\CommandHandler
 */
class CreateCompanyHandler
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
     * @param CreateCompanyCommand $command
     * @return string
     * @throws Exception
     */
    public function handle(CreateCompanyCommand $command): string
    {
        $isPreRegistered = $this->companyRepository->findOneBy(
            [
                'tin' => $command->getTin(),
            ]
        );

        if (!empty($isPreRegistered)) {
            $this->logger->critical(
                'Company has pre-registered with the TIN number provided',
                [
                    'tin' => $command->getTin(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company has pre-registered with the TIN number provided',
                Response::HTTP_BAD_REQUEST
            );
        }

        $isPreRegistered = $this->companyRepository->findOneBy(
            [
                'name' => $command->getName(),
            ]
        );

        if (!empty($isPreRegistered)) {
            $this->logger->critical(
                'Company has pre-registered with the name provided',
                [
                    'tin' => $command->getName(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company has pre-registered with the name provided',
                Response::HTTP_BAD_REQUEST
            );
        }

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
                'Company could not be registered',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company could not be registered. ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($isSaved) {
            $this->logger->debug(
                'Company registered successfully',
                [
                    'company_id' => $companyId->toString(),
                    'name' => $company->name(),
                    'tin' => $company->tin(),
                    'email' => $company->email(),
                ]
            );

            return $companyId->toString();
        }

        return '';
    }
}
