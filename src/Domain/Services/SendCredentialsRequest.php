<?php

declare(strict_types=1);

namespace App\Domain\Services;

/**
 * Class SendCredentialsRequest
 * @package App\Domain\Services
 */
class SendCredentialsRequest
{
    /** @var string */
    private string $reason;

    /** @var string */
    private string $username;

    /** @var string */
    private string $password;

    /** @var string */
    private string $email;

    /** @var string */
    private string $company;

    /**
     * @param string $reason
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $company
     */
    public function __construct(string $reason, string $username, string $password, string $email, string $company)
    {
        $this->reason = $reason;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }
}
