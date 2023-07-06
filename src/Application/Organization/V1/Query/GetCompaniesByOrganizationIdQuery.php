<?php

declare(strict_types=1);

namespace App\Application\Organization\V1\Query;

/**
 * Class GetCompaniesByOrganizationQuery
 * @package App\Application\Organization\V1\Query
 */
class GetCompaniesByOrganizationIdQuery
{
    /** @var string */
    protected string $organizationId;

    /** @var string */
    protected string $userType;

    /**
     * @param string $organizationId
     * @param string $userType
     */
    public function __construct(
        string $organizationId,
        string $userType
    ) {
        $this->userType = $userType;
        $this->organizationId = $organizationId;
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
