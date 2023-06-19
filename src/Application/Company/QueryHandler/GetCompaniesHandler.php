<?php

declare(strict_types=1);

namespace App\Application\Company\QueryHandler;

use App\Application\Company\Query\GetCompaniesQuery ;
use App\Domain\Model\Company\Company;
use App\Domain\Repository\CompanyRepository;
use Psr\Log\LoggerInterface;

/**
 * Class CreateCompanyHandler
 * @package App\Application\Company\QueryHandler
 */
class GetCompaniesHandler
{
    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(CompanyRepository $companyRepository, LoggerInterface $logger)
    {
        $this->companyRepository = $companyRepository;
        $this->logger = $logger;
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
            $traRegistration = $company['traRegistration'];
            unset($traRegistration['TAXCODES']);
            unset($traRegistration['USERNAME']);
            unset($traRegistration['PASSWORD']);

            $companiesData[] = [
                'companyId' => $company['companyId']->toString(),
                'organizationId' => $company['organizationId']->toString(),
                'name' => $company['name'],
                'tin' => $company['tin'],
                'address' => $company['address'],
                'phone' => $company['phone'],
                'email' => $company['email'],
                'traRegistration' => $traRegistration,
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
