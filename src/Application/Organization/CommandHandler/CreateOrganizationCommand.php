<?php

declare(strict_types=1);

namespace App\Application\Organization\CommandHandler;

/**
 * Class CreateOrganizationCommand
 * @package App\Application\Organization\CommandHandler
 */
class CreateOrganizationCommand
{
    /** @var string */
    private string $name;

    /** @var string */
    private string $ownerName;

    /** @var string */
    private string $ownerEmail;

    /** @var string|null */
    private ?string $ownerPhoneNumber;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    /**
     * @param string $ownerName
     */
    public function setOwnerName(string $ownerName): void
    {
        $this->ownerName = $ownerName;
    }

    /**
     * @return string
     */
    public function getOwnerEmail(): string
    {
        return $this->ownerEmail;
    }

    /**
     * @param string $ownerEmail
     */
    public function setOwnerEmail(string $ownerEmail): void
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
