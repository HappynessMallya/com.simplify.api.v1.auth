<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Infrastructure\Symfony\Security\UserEntity;

interface UserRepository
{
    public function get(UserId $userId): ?User;

    public function save(User $user): bool;

    public function remove(User $user): bool;

    public function login(UserId $userId): void;

    public function findOneBy(array $criteria): ?UserEntity;
}
