<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Organization\Organization;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Repository\OrganizationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class DoctrineOrganizationRepository
 * @package App\Infrastructure\Repository
 */
class DoctrineOrganizationRepository implements OrganizationRepository
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var EntityManager */
    private EntityManager $em;

    /** @var EntityRepository */
    private EntityRepository $repository;

    /**
     * @param LoggerInterface $logger
     * @param EntityManager $em
     */
    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em
    ) {
        $this->logger = $logger;
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

            throw new Exception('Exception error trying to save organization: ' . $exception->getMessage());
        }

        return true;
    }

    /**
     * @param int $page
     * @param int $pageSize
     * @param string|null $orderBy
     * @return array
     */
    public function getAll(
        int $page = 0,
        int $pageSize = 10,
        ?string $orderBy = 'createdAt'
    ): array {
        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $orderBy = empty($orderBy) ? 'createdAt' : $orderBy;
        $query = $this->repository->createQueryBuilder('o')
            ->orderBy('o.' . $orderBy, 'DESC')
            ->setFirstResult($pageSize * $page)
            ->setMaxResults($pageSize)
            ->where('o.enable = true')
            ->getQuery();

        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);
        $companies = $query->getResult(Query::HYDRATE_ARRAY);

        return [
            'total' => $totalItems,
            'pages' => $pagesCount,
            'result' => $companies,
        ];
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
}
