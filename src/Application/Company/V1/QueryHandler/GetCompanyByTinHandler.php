<?php

declare(strict_types=1);

namespace App\Application\Company\V1\QueryHandler;

use App\Application\Company\V1\Query\GetCompanyByTinQuery;
use App\Domain\Repository\CompanyRepository;

/**
 * Class GetCompanyByTinHandler
 * @package App\Application\Company\V1\QueryHandler
 */
class GetCompanyByTinHandler
{
    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
     * @param CompanyRepository $companyRepository
     */
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param GetCompanyByTinQuery $query
     * @return array
     */
    public function handle(GetCompanyByTinQuery $query): array
    {
        $criteria = [
            'tin' => $query->getTin(),
        ];

        $company = $this->companyRepository->findOneBy($criteria);

        if (empty($company)) {
            return [];
        }

        return [
            'companyId' => $company->companyId()->toString(),
            'organizationId' => $company->organizationId()->toString(),
            'name' => $company->name(),
            'tin' => $company->tin(),
            'email' => $company->email(),
            'address' => $company->address(),
            'traRegistration' => $company->traRegistration(),
            'createdAt' => $company->createdAt()->format('Y-m-d H:i:s'),
            'status' => $company->companyStatus(),
        ];
    }
}
