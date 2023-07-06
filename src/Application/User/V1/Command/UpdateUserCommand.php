<?php

declare(strict_types=1);

namespace App\Application\User\V1\Command;

/**
 * Class UpdateUserCommand
 * @package App\Application\User\V1\Command
 */
class UpdateUserCommand
{
    /** @var string */
    private string $username;

    /** @var string|null */
    private ?string $firstName;

    /** @var string|null */
    private ?string $lastName;

    /** @var string|null */
    private ?string $email;

    /** @var string|null */
    private ?string $mobileNumber;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return empty($this->firstName) ? null : $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return empty($this->lastName) ? null : $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return empty($this->email) ? null : $this->email;
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
    public function getMobileNumber(): ?string
    {
        return empty($this->mobileNumber) ? null : $this->mobileNumber;
    }

    /**
     * @param string|null $mobileNumber
     */
    public function setMobileNumber(?string $mobileNumber): void
    {
        $this->mobileNumber = $mobileNumber;
    }
}
