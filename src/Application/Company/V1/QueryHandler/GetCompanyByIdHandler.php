<?php

declare(strict_types=1);

namespace App\Application\Company\V1\QueryHandler;

use App\Application\Company\V1\Query\GetCompanyByIdQuery;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\OrganizationRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetCompanyByIdHandler
 * @package App\Application\Company\V1\QueryHandler
 */
class GetCompanyByIdHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var OrganizationRepository  */
    private OrganizationRepository $organizationRepository;

    /**
     * @param LoggerInterface $logger
     * @param CompanyRepository $companyRepository
     * @param OrganizationRepository $organizationRepository
     */
    public function __construct(
        LoggerInterface $logger,
        CompanyRepository $companyRepository,
        OrganizationRepository $organizationRepository
    ) {
        $this->logger = $logger;
        $this->companyRepository = $companyRepository;
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @param GetCompanyByIdQuery $query
     * @return array
     * @throws Exception
     */
    public function handle(GetCompanyByIdQuery $query): array
    {
        $companyId = CompanyId::fromString($query->getCompanyId());
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

        $organizationName = '';
        $organizationId = '';
        if (!empty($company->organizationId())) {
            $organization = $this->organizationRepository->get($company->organizationId());
            $organizationName = (!empty($organization)) ? $organization->getName() : '';
            $organizationId = $company->organizationId()->toString();
        }

        return [
            'organizationId' => $organizationId,
            'organization' => $organizationName,
            'companyId' => $company->companyId()->toString(),
            'name' => $company->name(),
            'tin' => $company->tin(),
            'email' => $company->email(),
            'address' => $company->address(),
            'traRegistration' => $company->traRegistration(),
            'status' => $company->companyStatus(),
            'createdAt' => $company->createdAt()->format('Y-m-d H:i:s'),
        ];
    }
}
