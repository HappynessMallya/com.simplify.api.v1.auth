<?php

namespace App\Domain\Repository;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\User\UserId;

/**
 * Interface CompanyByUserRepository
 * @package App\Domain\Repository
 */
interface CompanyByUserRepository
{
    /**
     * @param UserId $userId
     * @return array
     */
    public function getCompaniesByUser(UserId $userId): array;

    /**
     * @param OrganizationId $organizationId
     * @return array
     */
    public function getOperatorsByOrganization(OrganizationId $organizationId): array;

    /**
     * @param UserId $userId
     * @param array $companies
     */
    public function saveCompaniesToUser(UserId $userId, array $companies): void;

    /**
     * @param UserId $userId
     * @param CompanyId $companyId
     */
    public function changeStatusUserOverCompany(UserId $userId, CompanyId $companyId): void;

    /**
     * @param UserId $userId
     */
    public function removeCompanyByUserId(UserId $userId): void;
}
