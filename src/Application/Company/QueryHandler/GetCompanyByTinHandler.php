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
     * @return Company|null
     */
    public function handle(GetCompanyByTinQuery $query): ?Company
    {
        $criteria = [
            'tin' => $query->getCompanyTin(),
        ];

        return $this->companyRepository->findOneBy($criteria);
    }
}
