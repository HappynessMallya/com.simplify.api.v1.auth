<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class DoctrineCompanyRepository
 * @package App\Infrastructure\Repository
 */
class DoctrineCompanyRepository implements CompanyRepository
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
     * @throws NotSupported
     */
    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em
    ) {
        $this->logger = $logger;
        $this->em = $em;
        $this->repository = $this->em->getRepository(Company::class);
    }

    /**
     * @param CompanyId $companyId
     * @return Company|null
     */
    public function get(CompanyId $companyId): ?Company
    {
        return $this->repository->find($companyId);
    }

    /**
     * @param Company $company
     * @return bool
     * @throws Exception
     */
    public function save(Company $company): bool
    {
        try {
            $this->em->persist($company);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to save company',
                [
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception('Exception error trying to save company: ' . $exception->getMessage());
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
        $query = $this->repository->createQueryBuilder('c')
            ->orderBy('c.' . $orderBy, 'DESC')
            ->setFirstResult($pageSize * $page)
            ->setMaxResults($pageSize)
            ->where('c.enable = true')
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
     * @return Company|null
     */
    public function findOneBy(array $criteria): ?Company
    {
        if (empty($criteria)) {
            return null;
        }

        return $this->repository->findOneBy($criteria);
    }
}
