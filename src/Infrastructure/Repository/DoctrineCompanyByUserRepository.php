<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Domain\Repository\CompanyByUserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class DoctrineCompanyByUserRepository implements CompanyByUserRepository
{
    public const ENTITY_TABLE_NAME = "company_by_user";

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
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getCompaniesByUser(UserId $userId): array
    {
        $query = sprintf(/** @lang sql */
            'SELECT * FROM %s WHERE user_id = ? AND status = \'ACTIVE\'',
            self::ENTITY_TABLE_NAME
        );

        $result = $this->connection->executeQuery(
            $query,
            [
                $userId->toString()
            ]
        )->fetchAllAssociative();

        if (empty($result)) {
            return [];
        }

        return $result;
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
            self::ENTITY_TABLE_NAME
        );

        foreach ($companies as $index => $company) {
            if ($index < count($companies) - 1) {
                $query .= "('" . $userId->toString() . "', '" . $company . "', 'ACTIVE'),";
            } else {
                $query .= "('" . $userId->toString() . "', '" . $company . "', 'ACTIVE')";
            }
        }

        $this->connection->executeStatement($query);
    }

    /**
     * @inheritDoc
     */
    public function changeStatusUserOverCompany(UserId $userId, CompanyId $companyId): void
    {
        // TODO: Implement changeStatusUserOverCompany() method.
    }

    /**
     * @inheritDoc
     */
    public function removeCompanyByUserId(UserId $userId): void
    {
        // TODO: Implement removeCompanyByUserId() method.
    }
}
