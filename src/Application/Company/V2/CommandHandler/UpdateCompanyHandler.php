<?php

declare(strict_types=1);

namespace App\Application\Company\V2\CommandHandler;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\OrganizationRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateCompanyHandler
 * @package App\Application\Company\CommandHandler
 */
class UpdateCompanyHandler
{
    /** @var CompanyRepository  */
    private CompanyRepository $companyRepository;

    /** @var LoggerInterface  */
    private LoggerInterface $logger;

    /** @var OrganizationRepository  */
    private OrganizationRepository $organizationRepository;

    /**
     * @param CompanyRepository $companyRepository
     * @param LoggerInterface $logger
     * @param OrganizationRepository $organizationRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        LoggerInterface $logger,
        OrganizationRepository $organizationRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->logger = $logger;
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @param UpdateCompanyCommand $command
     * @return array
     * @throws Exception
     */
    public function handle(UpdateCompanyCommand $command): array
    {
        $companyId = CompanyId::fromString($command->getCompanyId());
        $organizationId = (!empty($command->getOrganizationId()))
            ? OrganizationId::fromString($command->getOrganizationId())
            : null;
        $company = $this->companyRepository->get($companyId);

        if (empty($company)) {
            $this->logger->critical(
                'Company not found',
                [
                    'company_id' => $companyId->toString(),
                    'method' => __METHOD__
                ]
            );

            throw new Exception('Company could not be found', 404);
        }

        if (!empty($organizationId) && !$company->organizationId()->sameValueAs($organizationId)) {
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

                throw new Exception('Organization could not be found', 404);
            }

            $company->setOrganizationId($organizationId);
        }

        $company->update([
            'name' => $command->getName(),
            'email' => $command->getEmail(),
            'address' => $command->getAddress(),
            'phone' => $command->getPhone(),
        ]);
        $company->setUpdatedAt(new DateTime('now'));

        try {
            $this->companyRepository->save($company);

            return [
                'companyId' => $companyId->toString(),
                'organizationId' => $companyId->toString(),
                'updatedAt' => $company->updatedAt()->format('Y-m-d H:i:s')
            ];
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal error has been occurred trying update company',
                [
                    'company_id' => $companyId->toString(),
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception('An internal error has been occurred trying update company', 500);
        }
    }
}
