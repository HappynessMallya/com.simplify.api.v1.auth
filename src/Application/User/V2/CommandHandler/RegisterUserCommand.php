<?php

declare(strict_types=1);

namespace App\Application\User\V2\CommandHandler;

/**
 * Class RegisterUserCommand
 * @package App\Application\User\V2\CommandHandler
 */
class RegisterUserCommand
{
    /** @var string */
    private string $userIdWhoRegister;

    /** @var string */
    private string $userTypeWhoRegister;

    /** @var array */
    private array $companies;

    /** @var string */
    private string $firstName;

    /** @var string */
    private string $lastName;

    /** @var string */
    private string $username;

    /** @var string */
    private string $email;

    /** @var string|null */
    private ?string $mobileNumber;

    /** @var string */
    private string $salt;

    /** @var string|null */
    private ?string $password = null;

    /** @var string|null */
    private ?string $confirmationToken;

    /** @var string|null */
    private ?string $role = null;

    /** @var string */
    private string $status;

    /** @var string */
    private string $userType;

    /**
     * @return string
     */
    public function getUserIdWhoRegister(): string
    {
        return $this->userIdWhoRegister;
    }

    /**
     * @param string $userIdWhoRegister
     */
    public function setUserIdWhoRegister(string $userIdWhoRegister): void
    {
        $this->userIdWhoRegister = $userIdWhoRegister;
    }

    /**
     * @return string
     */
    public function getUserTypeWhoRegister(): string
    {
        return $this->userTypeWhoRegister;
    }

    /**
     * @param string $userTypeWhoRegister
     */
    public function setUserTypeWhoRegister(string $userTypeWhoRegister): void
    {
        $this->userTypeWhoRegister = $userTypeWhoRegister;
    }

    /**
     * @return array
     */
    public function getCompanies(): array
    {
        return $this->companies;
    }

    /**
     * @param array $companies
     */
    public function setCompanies(array $companies): void
    {
        $this->companies = $companies;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
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
     * @return string
     */
    public function getEmail(): string
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

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return empty($this->password) ? null : $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getConfirmationToken(): ?string
    {
        return empty($this->confirmationToken) ? null : $this->confirmationToken;
    }

    /**
     * @param string|null $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return empty($this->role) ? null : $this->role;
    }

    /**
     * @param string|null $role
     */
    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @param string $userType
     */
    public function setUserType(string $userType): void
    {
        $this->userType = $userType;
    }
}
