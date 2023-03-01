<?php

declare(strict_types=1);

namespace App\Application\User\Command;

/**
 * Class UpdateUserCommand
 * @package App\Application\User\Command
 */
class UpdateUserCommand
{
    /** @var string */
    private string $userId;

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
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
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
