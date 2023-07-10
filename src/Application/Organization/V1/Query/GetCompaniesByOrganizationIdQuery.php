<?php

declare(strict_types=1);

namespace App\Application\Organization\V1\Query;

/**
 * Class GetCompaniesByOrganizationIdQuery
 * @package App\Application\Organization\V1\Query
 */
class GetCompaniesByOrganizationIdQuery
{
    /** @var string */
    private string $organizationId;

    /** @var string */
    private string $userType;

    /**
     * @param string $organizationId
     * @param string $userType
     */
    public function __construct(
        string $organizationId,
        string $userType
    ) {
        $this->organizationId = $organizationId;
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
    public function getUserType(): string
    {
        return $this->userType;
    }
}
