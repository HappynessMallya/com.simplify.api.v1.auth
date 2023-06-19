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
    private string $operatorId;

    /** @var string */
    private string $userType;

    /** @var string */
    private string $newStatus;

    /**
     * @param string $operatorId
     * @param string $userType
     * @param string $newStatus
     */
    public function __construct(string $operatorId, string $userType, string $newStatus)
    {
        $this->operatorId = $operatorId;
        $this->userType = $userType;
        $this->newStatus = $newStatus;
    }

    /**
     * @return string
     */
    public function getOperatorId(): string
    {
        return $this->operatorId;
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
