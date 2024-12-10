<?php

declare(strict_types=1);

namespace App\Application\Company\V2\Command;

/**
 * Class RegisterCompanyCommand
 * @package App\Application\Company\V2\Command
 */
class RegisterCompanyCommand
{
    /** @var string */
    private string $name;

    /** @var string */
    private string $tin;

    /** @var string|null */
    private ?string $address;

    /** @var string|null */
    private ?string $email;

    /** @var string|null */
    private ?string $phone;

    /** @var string|null */
    private ?string $serial;

    /** @var string|null */
    private ?string $organizationId;

    /** @var float|null */
    private ?float $subscriptionAmount;



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
     * @param float $subscriptionAmount
     */

    public function getSubscriptionAmount(): ?float
    {
        return $this->subscriptionAmount;
    }

    public function setSubscriptionAmount(?float $subscriptionAmount): void
    {
        $this->subscriptionAmount = $subscriptionAmount;
    }

    /**
     * @return string
     */
    public function getTin(): string
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
    public function getSerial(): ?string
    {
        return $this->serial;
    }

    /**
     * @param string|null $serial
     */
    public function setSerial(?string $serial): void
    {
        $this->serial = $serial;
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
