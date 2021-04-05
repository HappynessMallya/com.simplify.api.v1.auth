<?php
declare(strict_types=1);

namespace App\Application\Company\QueryHandler;

use App\Application\Company\Query\GetCompanyByIdQuery;
use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyRepository;

/**
 * Class GetCompanyByIdHandler
 * @package App\Application\Company\QueryHandler
 */
class GetCompanyByIdHandler
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function handle(GetCompanyByIdQuery $command): ?Company
    {
        return $this->companyRepository->get(CompanyId::fromString($command->companyId()));
    }
}