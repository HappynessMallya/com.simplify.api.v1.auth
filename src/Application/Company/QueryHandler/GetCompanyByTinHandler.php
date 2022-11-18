<?php

declare(strict_types=1);

namespace App\Application\Company\QueryHandler;

use App\Application\Company\Query\GetCompanyByTinQuery;
use App\Domain\Model\Company\Company;
use App\Domain\Repository\CompanyRepository;

/**
 * Class GetCompanyByTinHandler
 * @package App\Application\Company\QueryHandler
 */
class GetCompanyByTinHandler
{
    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
     * GetCompanyByTinHandler constructor
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
            'name' => $company->name(),
            'tin' => $company->tin(),
            'email' => $company->email(),
            'address' => $company->address(),
            'traRegistration' => $company->traRegistration(),
            'createdAt' => $company->createdAt()->format(DATE_ATOM),
            'status' => $company->companyStatus(),
        ];
    }
}
