<?php

declare(strict_types=1);

namespace App\Application\User\V1\Query;

/**
 * Class GetOperatorsByOrganizationIdQuery
 * @package App\Application\User\V1\Query
 */
class GetOperatorsByOrganizationQuery
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
