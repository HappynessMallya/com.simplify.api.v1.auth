<?php

declare(strict_types=1);

namespace App\Application\Organization\V1\Query;

/**
 * Class GetOrganizationByIdQuery
 * @package App\Application\Organization\V1\Query
 */
class GetOrganizationByIdQuery
{
    /** @var string */
    private string $organizationId;

    /**
     * @param string $organizationId
     */
    public function __construct(
        string $organizationId
    ) {
        $this->organizationId = $organizationId;
    }

    /**
     * @return string
     */
    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }
}
