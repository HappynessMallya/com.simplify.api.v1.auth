<?php

declare(strict_types=1);

namespace App\Application\User\V2\QueryHandler;

/**
 * Class GetCompaniesByUserTypeQuery
 * @package App\Application\User\V2\QueryHandler
 */
class GetCompaniesByUserTypeQuery
{
    /** @var string */
    protected string $userId;

    /** @var string */
    protected string $userType;

    /**
     * @param string $userId
     * @param string $userType
     */
    public function __construct(string $userId, string $userType)
    {
        $this->userId = $userId;
        $this->userType = $userType;
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
}
