<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\User\UserId;
use App\Domain\Repository\CompanyByUserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Class DoctrineCompanyByUserRepository
 * @package App\Infrastructure\Repository
 */
class DoctrineCompanyByUserRepository implements CompanyByUserRepository
{
    public const ORGANIZATION_TABLE = "organization";
    public const COMPANY_TABLE = "company";
    public const COMPANY_BY_USER_TABLE = "company_by_user";

    /** @var Connection */
    private Connection $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param UserId $userId
     * @return array
     * @throws Exception
     */
    public function getCompaniesByUser(UserId $userId): array
    {
        $query = sprintf(/** @lang sql */
            'SELECT * FROM %s WHERE user_id = ? AND status = \'ACTIVE\'',
            self::COMPANY_BY_USER_TABLE
        );

        return $this->connection->executeQuery(
            $query,
            [
                $userId->toString(),
            ]
        )->fetchAllAssociative();
    }

    /**
     * @param OrganizationId $organizationId
     * @return array
     * @throws Exception
     */
    public function getOperatorsByOrganization(OrganizationId $organizationId): array
    {
        $query = sprintf(/** @lang sql */
            'SELECT cbu.user_id, cbu.status
            FROM %s AS cbu
            JOIN %s AS c
                ON c.id = cbu.company_id
            JOIN %s AS o
                ON c.organization_id = o.organization_id',
            self::COMPANY_BY_USER_TABLE,
            self::COMPANY_TABLE,
            self::ORGANIZATION_TABLE
        );

        return $this->connection->executeQuery($query)->fetchAllAssociative();
    }

    /**
     * @param UserId $userId
     * @param array $companies
     * @return void
     * @throws Exception
     */
    public function saveCompaniesToUser(UserId $userId, array $companies): void
    {
        $query = sprintf(/** @lang sql */
            'INSERT INTO %s (user_id, company_id, status) VALUES ',
            self::COMPANY_BY_USER_TABLE
        );

        foreach ($companies as $index => $company) {
            if ($index < count($companies) - 1) {
                $query .= "('" . $userId->toString() . "', '" . $company . "', 'ACTIVE'), ";
            } else {
                $query .= "('" . $userId->toString() . "', '" . $company . "', 'ACTIVE')";
            }
        }

        $this->connection->executeStatement($query);
    }

    /**
     * @param UserId $userId
     * @param CompanyId $companyId
     */
    public function changeStatusUserOverCompany(UserId $userId, CompanyId $companyId): void
    {
        // TODO: Implement changeStatusUserOverCompany() method.
    }

    /**
     * @param UserId $userId
     */
    public function removeCompanyByUserId(UserId $userId): void
    {
        // TODO: Implement removeCompanyByUserId() method.
    }
}
