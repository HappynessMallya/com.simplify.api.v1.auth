<?php

declare(strict_types=1);

namespace App\Application\Organization\QueryHandler;

/**
 * Class GetCompaniesByParamsQuery
 * @package App\Application\Organization\QueryHandler
 */
class GetCompaniesByParamsQuery
{
    /** @var string */
    private string $organizationId;

    /** @var string */
    private string $userId;

    /** @var string */
    private string $userType;

    /** @var string */
    private string $companyName;

    /** @var string */
    private string $tin;

    /** @var string */
    private string $vrn;

    /** @var string */
    private string $email;

    /** @var string */
    private string $mobileNumber;

    /** @var string */
    private string $serial;

    /** @var string */
    private string $status;

    /**
     * @param string $organizationId
     * @param string $userId
     * @param string $userType
     * @param string $companyName
     * @param string $tin
     * @param string $vrn
     * @param string $email
     * @param string $mobileNumber
     * @param string $serial
     * @param string $status
     */
    public function __construct(
        string $organizationId,
        string $userId,
        string $userType,
        string $companyName,
        string $tin,
        string $vrn,
        string $email,
        string $mobileNumber,
        string $serial,
        string $status
    ) {
        $this->userId = $userId;
        $this->userType = $userType;
        $this->companyName = $companyName;
        $this->tin = $tin;
        $this->vrn = $vrn;
        $this->email = $email;
        $this->mobileNumber = $mobileNumber;
        $this->serial = $serial;
        $this->status = $status;
        $this->organizationId = $organizationId;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
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
    public function getVrn(): string
    {
        return $this->vrn;
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
    public function getMobileNumber(): string
    {
        return $this->mobileNumber;
    }

    /**
     * @return string
     */
    public function getSerial(): string
    {
        return $this->serial;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }
}
