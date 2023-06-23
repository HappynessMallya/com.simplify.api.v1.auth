<?php

declare(strict_types=1);

namespace App\Application\User\V2\QueryHandler;

/**
 * Class ChangeUserStatusByIdQuery
 * @package App\Application\User\V2\QueryHandler
 */
class ChangeUserStatusByIdQuery
{
    /** @var string */
    private string $userId;

    /** @var string */
    private string $userType;

    /** @var string */
    private string $newStatus;

    /**
     * @param string $userId
     * @param string $userType
     * @param string $newStatus
     */
    public function __construct(string $userId, string $userType, string $newStatus)
    {
        $this->userId = $userId;
        $this->userType = $userType;
        $this->newStatus = $newStatus;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @return string
     */
    public function getNewStatus(): string
    {
        return $this->newStatus;
    }
}
