<?php

declare(strict_types=1);

namespace App\Application\Organization\QueryHandler;

/**
 * Class ChangeOrganizationStatusByIdQuery
 * @package App\Application\Organization\QueryHandler
 */
class ChangeOrganizationStatusByIdQuery
{
    /** @var string */
    private string $organizationId;

    /** @var string */
    private string $newStatus;

    /**
     * @param string $organizationId
     * @param string $newStatus
     */
    public function __construct(string $organizationId, string $newStatus)
    {
        $this->organizationId = $organizationId;
        $this->newStatus = $newStatus;
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
    public function getNewStatus(): string
    {
        return $this->newStatus;
    }
}
