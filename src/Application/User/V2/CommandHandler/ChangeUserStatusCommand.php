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
    private string $newStatus;

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
    public function getNewStatus(): string
    {
        return $this->newStatus;
    }

    /**
     * @param string $newStatus
     */
    public function setNewStatus(string $newStatus): void
    {
        $this->newStatus = $newStatus;
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
