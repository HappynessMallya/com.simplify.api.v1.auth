<?php

declare(strict_types=1);

namespace App\Application\Organization\V1\QueryHandler;

use App\Application\Organization\V1\Query\GetCompaniesByOrganizationIdQuery;
use App\Domain\Model\Company\Company;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetCompaniesByOrganizationIdHandler
 * @package App\Application\Organization\V1\QueryHandler
 */
class GetCompaniesByOrganizationIdHandler
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
     * @param GetCompaniesByOrganizationIdQuery $query
     * @return array
     * @throws Exception
     */
    public function __invoke(GetCompaniesByOrganizationIdQuery $query): array
    {
        $organizationId = OrganizationId::fromString($query->getOrganizationId());
        $companies = $this->companyRepository->getCompaniesByOrganizationId($organizationId);

        if (empty($companies)) {
            $this->logger->critical(
                'Companies could not be found by organization',
                [
                    'organization_id' => $organizationId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Companies could not be found by organization',
                Response::HTTP_NOT_FOUND
            );
        }

        $companiesResult = [];

        /** @var Company $company */
        foreach ($companies as $company) {
            $companiesResult[] = [
                'companyId' => $company->companyId()->toString(),
                'name' => $company->name(),
                'tin' => $company->tin(),
                'serial' => $company->serial(),
                'email' => $company->email(),
                'phone' => $company->phone() ?? '',
                'address' => $company->address(),
                'traRegistration' => $company->traRegistration(),
                'status' => $company->companyStatus(),
                'enable' => $company->isEnable(),
                'createdAt' => $company->createdAt()->format('Y-m-d H:i:s'),
            ];
        }

        return $companiesResult;
    }
}
