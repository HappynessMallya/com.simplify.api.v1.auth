<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Company\Certificate;
use App\Domain\Model\Company\CertificateId;
use App\Domain\Model\Company\TaxIdentificationNumber;
use App\Domain\Repository\CertificateRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception;

/**
 * Class DoctrineCertificateRepository
 * @package App\Infrastructure\Repository
 */
class DoctrineCertificateRepository implements CertificateRepository
{
    public const ENTITY_TABLE_NAME = "certificate";

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
     * @throws Exception
     */
    public function save(array $filesPack): void
    {
        foreach ($filesPack as $file) {
            $query = sprintf(/** @lang sql */
                'INSERT INTO %s
                (
                    certificate_id,
                    tin,
                    filepath,
                    created_at,
                    updated_at
                )
            VALUES
                (?, ?, ?, now(), now())',
                self::ENTITY_TABLE_NAME
            );

            $this->connection->executeStatement(
                $query,
                [
                    $file->getCertificateId()->toString(),
                    $file->getTin()->value(),
                    $file->getFilepath(),
                ]
            );
        }
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     * @throws DBALException
     */
    public function findByCertificateId(CertificateId $certificateId): ?Certificate
    {
        $query = sprintf(/** @lang sql */
            'SELECT * FROM %s WHERE certificate_id = ?',
            self::ENTITY_TABLE_NAME
        );

        $result = $this->connection->executeQuery(
            $query,
            [
                $certificateId->toString(),
            ]
        )->fetchAssociative();

        if (empty($result)) {
            return null;
        }

        return new Certificate(
            CertificateId::fromString($result['certificate_id']),
            new TaxIdentificationNumber($result['tin']),
            $result['filepath']
        );
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     * @throws DBALException
     */
    public function findByFilePath(string $filePath): ?Certificate
    {
        $query = sprintf(/** @lang sql */
            'SELECT * FROM %s WHERE filepath = ?',
            self::ENTITY_TABLE_NAME
        );

        $result = $this->connection->executeQuery(
            $query,
            [
                $filePath,
            ]
        )->fetchAssociative();

        if (empty($result)) {
            return null;
        }

        return new Certificate(
            CertificateId::fromString($result['certificate_id']),
            new TaxIdentificationNumber($result['tin']),
            $result['filepath']
        );
    }

    /**
     * @throws Exception
     */
    public function update(Certificate $file): void
    {
        $query = sprintf(/** @lang sql */
            'UPDATE %s
            SET
                filepath = ?,
                updated_at = now()
            WHERE
                certificate_id = ?',
            self::ENTITY_TABLE_NAME
        );

        $this->connection->executeStatement(
            $query,
            [
                $file->getFilepath(),
                $file->getCertificateId()->toString(),
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function remove(CertificateId $certificateId): void
    {
        $query = sprintf(/** @lang sql */
            'UPDATE %s
            SET
                updated_at = now()
            WHERE
                certificate_id = ?',
            self::ENTITY_TABLE_NAME
        );

        $this->connection->executeStatement(
            $query,
            [
                $certificateId->toString(),
            ]
        );
    }
}
