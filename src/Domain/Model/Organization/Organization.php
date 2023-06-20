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
    private string $ownerName;

    /** @var string */
    private string $ownerEmail;

    /** @var string|null */
    private ?string $ownerPhoneNumber;

    /** @var string */
    private string $status;

    /** @var DateTime */
    private DateTime $createdAt;

    /** @var DateTime|null */
    private ?DateTime $updatedAt;

    /**
     * @param OrganizationId $organizationId
     * @param string $name
     * @param string $ownerName
     * @param string $ownerEmail
     * @param string|null $ownerPhoneNumber
     * @param OrganizationStatus $status
     * @param DateTime $createdAt
     * @param DateTime|null $updatedAt
     * @return Organization
     */
    public static function create(
        OrganizationId $organizationId,
        string $name,
        string $ownerName,
        string $ownerEmail,
        ?string $ownerPhoneNumber,
        OrganizationStatus $status,
        DateTime $createdAt,
        ?DateTime $updatedAt
    ): Organization {
        $self = new self();
        $self->organizationId = $organizationId;
        $self->name = $name;
        $self->ownerName = $ownerName;
        $self->ownerEmail = $ownerEmail;
        $self->ownerPhoneNumber = $ownerPhoneNumber;
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
     * @return string
     */
    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    /**
     * @return string
     */
    public function getOwnerEmail(): string
    {
        return $this->ownerEmail;
    }

    /**
     * @return string|null
     */
    public function getOwnerPhoneNumber(): ?string
    {
        return empty($this->ownerPhoneNumber) ? '' : $this->ownerPhoneNumber;
    }

    /**
     * @return OrganizationStatus
     */
    public function getStatus(): OrganizationStatus
    {
        return OrganizationStatus::byValue($this->status);
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return empty($this->updatedAt) ? null : $this->updatedAt;
    }
}
