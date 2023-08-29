<?php

declare(strict_types=1);

namespace App\Application\User\V2\Command;

/**
 * Class ChangeEnableUserCommand
 * @package App\Application\User\V2\Command
 */
class ChangeEnableUserCommand
{
    /** @var string */
    private string $userId;

    /** @var bool */
    private bool $enable;

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     */
    public function setEnable(bool $enable): void
    {
        $this->enable = $enable;
    }
}
