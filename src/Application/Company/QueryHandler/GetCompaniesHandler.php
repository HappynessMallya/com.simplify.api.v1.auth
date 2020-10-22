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
    private $companyRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(CompanyRepository $companyRepository, LoggerInterface $logger)
    {
        $this->companyRepository = $companyRepository;
        $this->logger = $logger;
    }

    public function handle(GetCompaniesQuery $command): array
    {
        $results = [];

        try {
            $companies = $this->companyRepository->getAll(
                $command->getPage(),
                $command->getPageSize(),
                $command->getOrderBy()
            );

            /** @var Company $company */
            foreach ($companies['result'] as $company) {
                $results[] = [
                    'companyId' => $company['companyId']->toString(),
                    'name' => $company['name'],
                    'address' => $company['address'],
                    'email' => $company['email'],
                ];
            }

            $results = [
                'total' => $companies['total'],
                'pages' => $companies['pages'],
                'page' => $command->getPage(),
                'records' => count($results),
                'data' => $results,
            ];
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), [__METHOD__]);
        }

        return $results;
    }
}
