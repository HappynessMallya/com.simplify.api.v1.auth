<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Infrastructure\Symfony\Security\UserEntity;

/**
 * Interface UserRepository
 * @package App\Domain\Repository
 */
interface UserRepository
{
    /**
     * @param UserId $userId
     * @return User|null
     */
    public function get(UserId $userId): ?User;

    /**
     * @param User $user
     * @return bool
     */
    public function save(User $user): bool;

    /**
     * @param User $user
     * @return bool
     */
    public function remove(User $user): bool;

    /**
     * @param UserId $userId
     */
    public function login(UserId $userId): void;

    /**
     * @param array $criteria
     * @return UserEntity|null
     */
    public function findOneBy(array $criteria): ?UserEntity;

    /**
     * @param array $criteria
     * @return User|null
     */
    public function findByCriteria(array $criteria): ?array;
}
