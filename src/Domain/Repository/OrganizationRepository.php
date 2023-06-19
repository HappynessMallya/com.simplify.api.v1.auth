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
     * @param int $page
     * @param int $pageSize
     * @param string|null $orderBy
     * @return array
     */
    public function getAll(int $page, int $pageSize, ?string $orderBy): array;

    /**
     * @param array $criteria
     * @return Organization|null
     */
    public function findOneBy(array $criteria): ?Organization;
}
