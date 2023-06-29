<?php

declare(strict_types=1);

namespace App\Application\Organization\CommandHandler;

/**
 * Class UpdateOrganizationCommand
 * @package App\Application\Organization\CommandHandler
 */
class UpdateOrganizationCommand
{
    /** @var string */
    private string $organizationId;

    /** @var string|null */
    private ?string $name;

    /** @var string|null */
    private ?string $ownerName;

    /** @var string|null */
    private ?string $ownerEmail;

    /** @var string|null */
    private ?string $ownerPhoneNumber;

    /**
     * @return string
     */
    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    /**
     * @param string $organizationId
     */
    public function setOrganizationId(string $organizationId): void
    {
        $this->organizationId = $organizationId;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return empty($this->name) ? '' : $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getOwnerName(): ?string
    {
        return empty($this->ownerName) ? '' : $this->ownerName;
    }

    /**
     * @param string|null $ownerName
     */
    public function setOwnerName(?string $ownerName): void
    {
        $this->ownerName = $ownerName;
    }

    /**
     * @return string|null
     */
    public function getOwnerEmail(): ?string
    {
        return empty($this->ownerEmail) ? '' : $this->ownerEmail;
    }

    /**
     * @param string|null $ownerEmail
     */
    public function setOwnerEmail(?string $ownerEmail): void
    {
        $this->ownerEmail = $ownerEmail;
    }

    /**
     * @return string|null
     */
    public function getOwnerPhoneNumber(): ?string
    {
        return empty($this->ownerPhoneNumber) ? '' : $this->ownerPhoneNumber;
    }

    /**
     * @param string|null $ownerPhoneNumber
     */
    public function setOwnerPhoneNumber(?string $ownerPhoneNumber): void
    {
        $this->ownerPhoneNumber = $ownerPhoneNumber;
    }
}
