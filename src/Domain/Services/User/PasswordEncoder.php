<?php

declare(strict_types=1);

namespace App\Domain\Services\User;

interface PasswordEncoder
{
    public function getSalt(): ?string;

    public function hashPassword(string $plainPassword): ?string;
}
