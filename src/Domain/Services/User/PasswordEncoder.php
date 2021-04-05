<?php

declare(strict_types=1);

namespace App\Domain\Services\User;

use App\Domain\Model\User\User;

interface PasswordEncoder
{
    public function getSalt(): ?string;

    public function hashPassword(User $user): ?string;
}
