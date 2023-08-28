<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Organization\Organization;
use App\Domain\Model\Organization\OrganizationId;

/**
 * Interface OrganizationRepository
 * @package App\Domain\Repository
 */
interface OrganizationRepository
{
    /**
     * @param OrganizationId $organizationId
     * @return Organization|null
     */
    public function get(OrganizationId $organizationId): ?Organization;

    /**
     * @param Organization $organization
     * @return bool
     */
    public function save(Organization $organization): bool;

    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param OrganizationId $organizationId
     * @return array
     */
    public function getCompaniesByOrganization(OrganizationId $organizationId): array;

    /**
     * @param array $criteria
     * @return Organization|null
     */
    public function findOneBy(array $criteria): ?Organization;

    /**
     * @param array $criteria
     * @return array|null
     */
    public function findByCriteria(array $criteria): ?array;
}
