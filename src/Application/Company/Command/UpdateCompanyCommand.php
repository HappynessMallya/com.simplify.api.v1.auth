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
    protected $companyId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $tin;

    /**
     * @var string|null
     */
    protected $address;

    /**
     * @var string|null
     */
    protected $email;

    /**
     * @var array|null
     */
    protected $traRegistration;

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
     * @return array|null
     */
    public function getTraRegistration(): ?array
    {
        return $this->traRegistration;
    }

    /**
     * @param string|null $traRegistration
     */
    public function setTraRegistration(?string $traRegistration): void
    {
        $this->traRegistration = json_decode($traRegistration, true);
    }
}
