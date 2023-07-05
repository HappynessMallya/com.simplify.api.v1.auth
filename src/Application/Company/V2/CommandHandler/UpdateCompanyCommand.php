<?php

declare(strict_types=1);

namespace App\Application\Company\V2\CommandHandler;

/**
 * Class UpdateCompanyCommand
 * @package App\Application\Company\V2\CommandHandler
 */
class UpdateCompanyCommand
{
    /** @var string */
    private string $companyId;

    /** @var string  */
    private string $name;

    /** @var string|null  */
    private ?string $address;

    /** @var string|null  */
    private ?string $email;

    /** @var string|null  */
    private ?string $phone;

    /** @var string|null  */
    private ?string $organizationId;

    /**
     * @return string
     */
    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    /**
     * @param string $companyId
     */
    public function setCompanyId(string $companyId): void
    {
        $this->companyId = $companyId;
    }

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
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string|null
     */
    public function getOrganizationId(): ?string
    {
        return $this->organizationId;
    }

    /**
     * @param string|null $organizationId
     */
    public function setOrganizationId(?string $organizationId): void
    {
        $this->organizationId = $organizationId;
    }
}
