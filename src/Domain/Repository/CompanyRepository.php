<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Organization\OrganizationId;

/**
 * Interface CompanyRepository
 * @package App\Domain\Repository
 */
interface CompanyRepository
{
    /**
     * @param CompanyId $companyId
     * @return Company|null
     */
    public function get(CompanyId $companyId): ?Company;

    /**
     * @param Company $company
     * @return bool
     */
    public function save(Company $company): bool;

    /**
     * @param int $page
     * @param int $pageSize
     * @param string|null $orderBy
     * @return array
     */
    public function getAll(int $page, int $pageSize, ?string $orderBy): array;

    /**
     * @param array $criteria
     * @return Company|null
     */
    public function findOneBy(array $criteria): ?Company;

    /**
     * @param OrganizationId $organizationId
     * @return array
     */
    public function getCompaniesByOrganizationId(OrganizationId $organizationId): array;

    /**
     * @param OrganizationId $organizationId
     * @param array $criteria
     * @return array
     */
    public function getByOrganizationIdAndParams(OrganizationId $organizationId, array $criteria): array;
}
