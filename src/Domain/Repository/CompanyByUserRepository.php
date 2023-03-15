<?php

namespace App\Domain\Repository;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;

interface CompanyByUserRepository
{
    /**
     * @param UserId $userId
     * @return array
     */
    public function getCompaniesByUser(UserId $userId): array;

    /**
     * @param UserId $userId
     * @param array $companies
     * @return void
     */
    public function saveCompaniesToUser(UserId $userId, array $companies): void;

    /**
     * @param UserId $userId
     * @param CompanyId $companyId
     * @return void
     */
    public function changeStatusUserOverCompany(UserId $userId, CompanyId $companyId): void;

    /**
     * @param UserId $userId
     * @return void
     */
    public function removeCompanyByUserId(UserId $userId): void;
}
