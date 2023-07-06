<?php

declare(strict_types=1);

namespace App\Application\User\V1\Command;

/**
 * Class VerifyUserExistsCommand
 * @package App\Application\User\V1\Command
 */
class VerifyUserExistsCommand
{
    /** @var string */
    private string $username;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
}
