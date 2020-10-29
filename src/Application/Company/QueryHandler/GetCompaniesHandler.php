<?php
declare(strict_types=1);

namespace App\Application\Company\QueryHandler;

use App\Application\Company\Query\GetCompaniesQuery ;
use App\Domain\Model\Company\Company;
use App\Domain\Repository\CompanyRepository;

/**
 * Class CreateCompanyHandler
 * @package App\Application\Company\QueryHandler
 */
class GetCompaniesHandler
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

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
            $companiesData[] = [
                'companyId' => $company['companyId']->toString(),
                'name' => $company['name'],
                'tin' => $company['tin'],
                'address' => $company['address'],
                'email' => $company['email'],
                'traRegistration' => $company['traRegistration'],
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
