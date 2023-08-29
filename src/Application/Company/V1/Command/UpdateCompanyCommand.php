<?php

declare(strict_types=1);

namespace App\Application\Company\V1\Command;

/**
 * Class UpdateCompanyCommand
 * @package App\Application\Company\V1\Command
 */
class UpdateCompanyCommand
{
    /** @var string|null */
    private ?string $organizationId;

    /** @var string */
    private string $companyId;

    /** @var string */
    private string $name;

    /** @var string|null */
    private ?string $email;

    /** @var string|null */
    private ?string $phone;

    /** @var string|null */
    private ?string $address;

    /**
     * @return string|null
     */
    public function getOrganizationId(): ?string
    {
        return empty($this->organizationId) ? null : $this->organizationId;
    }

    /**
     * @param string|null $organizationId
     */
    public function setOrganizationId(?string $organizationId): void
    {
        $this->organizationId = $organizationId;
    }

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
    public function getName(): ?string
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
    public function getEmail(): ?string
    {
        return empty($this->email) ? null : $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return empty($this->phone) ? null : $this->phone;
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
    public function getAddress(): ?string
    {
        return empty($this->address) ? null : $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }
}
