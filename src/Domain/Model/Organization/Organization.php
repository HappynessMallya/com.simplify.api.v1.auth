<?php

declare(strict_types=1);

namespace App\Domain\Model\Organization;

use DateTime;

/**
 * Class Organization
 * @package App\Domain\Model\Company
 */
class Organization
{
    /** @var OrganizationId */
    private OrganizationId $organizationId;

    /** @var string */
    private string $name;

    /** @var string */
    private string $status;

    /** @var DateTime */
    private DateTime $createdAt;

    /** @var DateTime */
    private DateTime $updatedAt;

    /**
     * @param OrganizationId $organizationId
     * @param string $name
     * @param OrganizationStatus $status
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     * @return Organization
     */
    public static function create(
        OrganizationId $organizationId,
        string $name,
        OrganizationStatus $status,
        DateTime $createdAt,
        DateTime $updatedAt
    ): Organization {
        $self = new self();
        $self->organizationId = $organizationId;
        $self->name = $name;
        $self->status = $status->getValue();
        $self->createdAt = $createdAt;
        $self->updatedAt = $updatedAt;

        return $self;
    }

    /**
     * @return OrganizationId
     */
    public function getOrganizationId(): OrganizationId
    {
        return $this->organizationId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return OrganizationStatus
     */
    public function getStatus(): OrganizationStatus
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
