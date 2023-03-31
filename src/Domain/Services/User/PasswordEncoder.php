<?php

declare(strict_types=1);

namespace App\Domain\Services\User;

use App\Domain\Model\User\User;
use App\Infrastructure\Symfony\Security\UserEntity;

/**
 * Interface PasswordEncoder
 * @package App\Domain\Services\User
 */
interface PasswordEncoder
{
    /**
     * @return string|null
     */
    public function getSalt(): ?string;

    /**
     * @param User $user
     * @return string|null
     */
    public function hashPassword(User $user): ?string;

    /**
     * @param UserEntity $user
     * @param string $raw
     * @return bool
     */
    public function isPasswordValid(UserEntity $user, string $raw): bool;
}
