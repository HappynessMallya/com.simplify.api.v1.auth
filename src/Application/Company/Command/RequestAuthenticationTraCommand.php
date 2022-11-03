<?php

namespace App\Application\Company\Command;

class RequestAuthenticationTraCommand
{
    /**
     * @var string
     */
    private string $companyId;

    /**
     * @var string
     */
    private string $tin;

    /**
     * @var string
     */
    private string $username;

    /**
     * @var string
     */
    private string $password;

    /**
     * @param string $companyId
     * @param string $tin
     * @param string $username
     * @param string $password
     */
    public function __construct(string $companyId, string $tin, string $username, string $password)
    {
        $this->companyId = $companyId;
        $this->tin = $tin;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getTin(): string
    {
        return $this->tin;
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
}