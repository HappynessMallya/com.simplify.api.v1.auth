<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Organization\Organization;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\Organization\OrganizationStatus;
use App\Domain\Repository\OrganizationRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DoctrineOrganizationRepository
 * @package App\Infrastructure\Repository
 */
class DoctrineOrganizationRepository implements OrganizationRepository
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var Connection */
    private Connection $connection;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $em;

    /** @var EntityRepository */
    private EntityRepository $repository;

    public const TABLE_ORGANIZATION = 'organization';

    /**
     * @param LoggerInterface $logger
     * @param Connection $connection
     * @param EntityManagerInterface $em
     */
    public function __construct(
        LoggerInterface $logger,
        Connection $connection,
        EntityManagerInterface $em
    ) {
        $this->logger = $logger;
        $this->connection = $connection;
        $this->em = $em;
        $this->repository = $this->em->getRepository(Organization::class);
    }

    /**
     * @param OrganizationId $organizationId
     * @return Organization|null
     */
    public function get(OrganizationId $organizationId): ?Organization
    {
        return $this->repository->find($organizationId);
    }

    /**
     * @param Organization $organization
     * @return bool
     * @throws Exception
     */
    public function save(Organization $organization): bool
    {
        try {
            $this->em->persist($organization);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to save organization',
                [
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Exception error trying to save organization: ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return true;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getAll(): array {
        $query = sprintf(/** @lang sql */"
            SELECT
                COUNT(*) AS totalOrganizations
            FROM %s AS o",
            self::TABLE_ORGANIZATION
        );

        try {
            $organizations = $this->connection->executeQuery($query)->fetchAssociative();
        } catch (Exception
            | \Doctrine\DBAL\Driver\Exception $exception) {
            $this->logger->critical(
                'Exception error trying to get organizations',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Exception error trying to get organizations. ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if (empty($organizations)) {
            return [];
        }

        return $organizations;
    }

    /**
     * @param OrganizationId $organizationId
     * @return array
     * @throws Exception
     */
    public function getCompaniesByOrganization(OrganizationId $organizationId): array {
        $query = sprintf(/** @lang sql */"
            SELECT
                COUNT(c.id) AS companiesQuantity
            FROM %s AS o
                LEFT JOIN company AS c
                    ON o.organization_id = c.organization_id
            WHERE o.organization_id = ?
            GROUP BY o.name",
            self::TABLE_ORGANIZATION
        );

        try {
            $companiesByOrganization = $this->connection->executeQuery(
                $query,
                [
                    $organizationId->toString(),
                ]
            )->fetchAssociative();
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to get companies by organization',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Exception error trying to get companies by organization. ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if (empty($companiesByOrganization)) {
            return [];
        }

        return $companiesByOrganization;
    }

    /**
     * @param array $criteria
     * @return Organization|null
     */
    public function findOneBy(array $criteria): ?Organization
    {
        if (empty($criteria)) {
            return null;
        }

        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param array $criteria
     * @return array|null
     */
    public function findByCriteria(array $criteria): ?array
    {
        if (empty($criteria['status'])) {
            $criteria['status'] = [
                OrganizationStatus::ACTIVE(),
                OrganizationStatus::INACTIVE(),
            ];
        }

        return $this->repository->findBy($criteria);
    }
}
