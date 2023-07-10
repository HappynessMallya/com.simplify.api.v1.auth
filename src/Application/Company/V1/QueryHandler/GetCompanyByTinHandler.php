<?php

declare(strict_types=1);

namespace App\Application\Company\V1\QueryHandler;

use App\Application\Company\V1\Query\GetCompanyByTinQuery;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetCompanyByTinHandler
 * @package App\Application\Company\V1\QueryHandler
 */
class GetCompanyByTinHandler
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
     * @param GetCompanyByTinQuery $query
     * @return array
     * @throws Exception
     */
    public function handle(GetCompanyByTinQuery $query): array
    {
        if (strlen($query->getTin()) != 9) {
            $this->logger->critical(
                'Invalid TIN number provided',
                [
                    'tin' => $query->getTin(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Invalid TIN number provided',
                Response::HTTP_BAD_REQUEST
            );
        }

        $criteria = [
            'tin' => $query->getTin(),
        ];

        $company = $this->companyRepository->findOneBy($criteria);

        if (empty($company)) {
            $this->logger->critical(
                'Company could not be found',
                [
                    'tin' => $query->getTin(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        return [
            'organizationId' => $company->organizationId()->toString(),
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
