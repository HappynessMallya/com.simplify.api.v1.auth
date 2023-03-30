<?php

declare(strict_types=1);

namespace App\Application\User\V2\QueryHandler;

/**
 * Class GetUsersByOrganizationQuery
 * @package App\Application\User\V2\QueryHandler
 */
class GetUsersByOrganizationQuery
{
    /** @var string */
    protected string $organizationId;

    /** @var string */
    protected string $companyId;

    /** @var string */
    protected string $userId;

    /** @var string */
    protected string $userType;

    /**
     * @param string $organizationId
     * @param string $companyId
     * @param string $userId
     * @param string $userType
     */
    public function __construct(string $organizationId, string $companyId, string $userId, string $userType)
    {
        $this->organizationId = $organizationId;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->userType = $userType;
    }

    /**
     * @return string
     */
    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    /**
     * @return string
     */
    public function getCompanyId(): string
    {
        return $this->companyId;
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
