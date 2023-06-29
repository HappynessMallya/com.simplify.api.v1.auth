<?php

declare(strict_types=1);

namespace App\Application\User\V2\CommandHandler;

/**
 * Class ChangeUserStatusCommand
 * @package App\Application\User\V2\CommandHandler
 */
class ChangeUserStatusCommand
{
    /** @var string */
    private string $userId;

    /** @var string */
    private string $status;

    /** @var string|null */
    private ?string $userType;

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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getUserType(): ?string
    {
        return empty($this->userType) ? null : $this->userType;
    }

    /**
     * @param string|null $userType
     */
    public function setUserType(?string $userType): void
    {
        $this->userType = $userType;
    }
}
