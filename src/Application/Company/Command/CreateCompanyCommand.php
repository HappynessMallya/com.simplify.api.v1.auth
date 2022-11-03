<?php

declare(strict_types=1);

namespace App\Application\Company\Command;

/**
 * Class CreateCompanyCommand
 * @package App\Application\Company\Command
 */
class CreateCompanyCommand
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $tin;

    /**
     * @var string|null
     */
    protected ?string $address;

    /**
     * @var string|null
     */
    protected ?string $email;

    /**
     * @var string|null
     */
    protected ?string $phone;

    /**
     * @var string|null
     */
    protected ?string $serial;

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
}
