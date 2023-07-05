<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\UpdateCompanyCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\OrganizationRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UpdateCompanyHandler
 * @package App\Application\Company\CommandHandler
 */
class UpdateCompanyHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var OrganizationRepository */
    private OrganizationRepository $organizationRepository;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
     * @param LoggerInterface $logger
     * @param OrganizationRepository $organizationRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        LoggerInterface $logger,
        OrganizationRepository $organizationRepository,
        CompanyRepository $companyRepository
    ) {
        $this->logger = $logger;
        $this->organizationRepository = $organizationRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param UpdateCompanyCommand $command
     * @return bool|null
     * @throws Exception
     */
    public function handle(UpdateCompanyCommand $command): ?bool
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

        if (!empty($command->getOrganizationId())) {
            $organizationId = OrganizationId::fromString($command->getOrganizationId());
            $organization = $this->organizationRepository->get($organizationId);

            if (empty($organization)) {
                $this->logger->critical(
                    'Organization could not be found',
                    [
                        'organization_id' => $organizationId->toString(),
                        'company_id' => $companyId->toString(),
                        'method' => __METHOD__,
                    ]
                );

                throw new Exception(
                    'Organization could not be found',
                    Response::HTTP_NOT_FOUND
                );
            }

            $company->setOrganizationId($organizationId);
        }

        $company->update(
            [
                'name' => $command->getName(),
                'email' => $command->getEmail(),
                'phone' => $command->getPhone(),
                'address' => $command->getAddress(),
                'updatedAt' => new DateTime('now'),
            ]
        );

        try {
            $response = $this->companyRepository->save($company);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Company could not be updated',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response;
    }
}
