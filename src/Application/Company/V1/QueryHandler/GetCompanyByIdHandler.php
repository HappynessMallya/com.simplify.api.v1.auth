<?php

declare(strict_types=1);

namespace App\Application\Company\V1\QueryHandler;

use App\Application\Company\V1\Query\GetCompanyByIdQuery;
use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyRepository;

/**
 * Class GetCompanyByIdHandler
 * @package App\Application\Company\V1\QueryHandler
 */
class GetCompanyByIdHandler
{
    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        CompanyRepository $companyRepository
    ) {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param GetCompanyByIdQuery $command
     * @return Company|null
     */
    public function handle(GetCompanyByIdQuery $command): ?Company
    {
        return $this->companyRepository->get(CompanyId::fromString($command->companyId()));
    }
}
