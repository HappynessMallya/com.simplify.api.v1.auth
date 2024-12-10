<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Subscription\Subscription;

interface SubscriptionRepository
{
    public function save(Subscription $subscription): void;

    public function findByCompanyId(string $companyId): ?Subscription;
}
