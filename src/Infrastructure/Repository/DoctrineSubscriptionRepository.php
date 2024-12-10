<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Subscription\Subscription;
use App\Domain\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineSubscriptionRepository implements SubscriptionRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Subscription $subscription): void
    {
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }

    public function findByCompanyId(string $companyId): ?Subscription
    {
        return $this->entityManager->getRepository(Subscription::class)
            ->findOneBy(['companyId' => $companyId]);
    }
}
