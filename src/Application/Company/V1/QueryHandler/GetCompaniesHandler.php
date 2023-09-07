<?php

declare(strict_types=1);

namespace App\Application\Company\V1\QueryHandler;

use App\Application\Company\V1\Query\GetCompaniesQuery;
use App\Domain\Model\Company\Company;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\OrganizationRepository;

/**
 * Class CreateCompanyHandler
 * @package App\Application\Company\V1\QueryHandler
 */
class GetCompaniesHandler
{
    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var OrganizationRepository  */
    private OrganizationRepository $organizationRepository;

    /**
     * @param CompanyRepository $companyRepository
     * @param OrganizationRepository $organizationRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        OrganizationRepository $organizationRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @param GetCompaniesQuery $command
     * @return array
     */
    public function handle(GetCompaniesQuery $command): array
    {
        $companiesData = [];

        $companies = $this->companyRepository->getAll(
            $command->getPage(),
            $command->getPageSize(),
            $command->getOrderBy()
        );

        /** @var Company $company */
        foreach ($companies['result'] as $company) {
            $traRegistration = $company['traRegistration'];
            unset($traRegistration['TAXCODES']);
            unset($traRegistration['USERNAME']);
            unset($traRegistration['PASSWORD']);

            $organizationName = '';
            $organizationId = '';
            if (!empty($company['organizationId'])) {
                $organization = $this->organizationRepository->get($company['organizationId']);
                $organizationName = (!empty($organization)) ? $organization->getName() : '';
                $organizationId = $company['organizationId']->toString();
            }

            $companiesData[] = [
                'companyId' => $company['companyId']->toString(),
                'organizationId' => $organizationId,
                'organization' => $organizationName,
                'name' => $company['name'],
                'tin' => $company['tin'],
                'serial' => $company['serial'],
                'address' => $company['address'],
                'phone' => $company['phone'],
                'email' => $company['email'],
                'traRegistration' => $traRegistration,
                'enable' => $company['enable'],
            ];
        }

        return [
            'total' => $companies['total'],
            'pages' => $companies['pages'],
            'page' => $command->getPage(),
            'records' => count($companiesData),
            'data' => $companiesData,
        ];
    }
}
