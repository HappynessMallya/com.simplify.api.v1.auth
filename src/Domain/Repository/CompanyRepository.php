<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;

interface CompanyRepository
{
    public function get(CompanyId $companyId): ?Company;

    public function save(Company $company): bool;

    public function getAll(int $page, int $pageSize, ?string $orderBy): array;

    public function findOneBy(array $criteria): ?Company;
}
