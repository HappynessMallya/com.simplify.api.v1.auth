<?php

declare(strict_types=1);

namespace App\Application\Organization\QueryHandler;

/**
 * Class GetCompaniesByOrganizationQuery
 * @package App\Application\User\V2\QueryHandler
 */
class GetCompaniesByOrganizationQuery
{
    /** @var string  */
    protected string $organizationId;

    /** @var string */
    protected string $userId;

    /** @var string */
    protected string $userType;

    /**
     * @param string $organizationId
     * @param string $userId
     * @param string $userType
     */
    public function __construct(string $organizationId, string $userId, string $userType)
    {
        $this->userId = $userId;
        $this->userType = $userType;
        $this->organizationId = $organizationId;
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
    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }
}
