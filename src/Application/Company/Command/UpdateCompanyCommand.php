<?php

declare(strict_types=1);

namespace App\Application\Company\Command;

/**
 * Class UpdateCompanyCommand
 * @package App\Application\Company\Command
 */
final class UpdateCompanyCommand
{
    /**
     * @var string
     */
    private string $companyId;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $tin;

    /**
     * @var string|null
     */
    private ?string $address;

    /**
     * @var string|null
     */
    private ?string $email;

    /**
     * @var string|null
     */
    private ?string $phone;

    /**
     * @var bool|null
     */
    private ?bool $enable;

    /**
     * @var array|null
     */
    private ?array $traRegistration;

    /**
     * @var string
     */
    private string $companyStatus;
    
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
     * @return string
     */
    public function getTin(): ?string
    {
        return $this->tin;
    }

    /**
     * @param string $tin
     */
    public function setTin(string $tin): void
    {
        $this->tin = $tin;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
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
     * @return array|null
     */
    public function getTraRegistration(): ?array
    {
        return $this->traRegistration;
    }

    /**
     * @return bool|null
     */
    public function getEnable(): ?bool
    {
        return $this->enable ?? true;
    }

    /**
     * @param bool|null $enable
     */
    public function setEnable(?bool $enable): void
    {
        $this->enable = $enable ?? true;
    }

    /**
     * @param string|null $traRegistration
     */
    public function setTraRegistration(?string $traRegistration): void
    {
        $this->traRegistration = json_decode($traRegistration, true);
    }

    /**
     * @return string
     */
    public function getCompanyStatus(): string
    {
        return $this->companyStatus;
    }

    /**
     * @param string $companyStatus
     */
    public function setCompanyStatus(string $companyStatus): void
    {
        $this->companyStatus = $companyStatus;
    }
}
