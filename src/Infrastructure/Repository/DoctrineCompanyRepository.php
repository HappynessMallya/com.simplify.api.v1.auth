<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class DoctrineCompanyRepository
 * @package App\Infrastructure\Repository
 */
class DoctrineCompanyRepository implements CompanyRepository
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(Company::class);
    }

    public function get(CompanyId $companyId): ?Company
    {
        return $this->repository->find($companyId);
    }

    public function save(Company $company): bool
    {
        try {
            $this->em->persist($company);
            $this->em->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function getAll(int $page = 0, int $pageSize = 10, ?string $orderBy = 'createdAt'): array
    {
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

    public function findOneBy(array $criteria): ?Company
    {
        if (empty($criteria)) {
            return null;
        }

        return $this->repository->findOneBy($criteria);
    }
}
