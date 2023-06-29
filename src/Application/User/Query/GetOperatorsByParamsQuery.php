<?php

declare(strict_types=1);

namespace App\Application\User\Query;

/**
 * Class GetOperatorsByParamsQuery
 * @package App\Application\User\V2\QueryHandler
 */
class GetOperatorsByParamsQuery
{
    /** @var string  */
    private string $organizationId;

    /** @var string */
    private string $userId;

    /** @var string */
    private string $userType;

    /** @var string */
    private string $firstName;

    /** @var string */
    private string $lastName;

    /** @var string */
    private string $email;

    /** @var string */
    private string $mobileNumber;

    /** @var string */
    private string $status;

    /**
     * @param string $organizationId
     * @param string $userId
     * @param string $userType
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $mobileNumber
     * @param string $status
     */
    public function __construct(
        string $organizationId,
        string $userId,
        string $userType,
        string $firstName,
        string $lastName,
        string $email,
        string $mobileNumber,
        string $status
    ) {
        $this->userId = $userId;
        $this->userType = $userType;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->mobileNumber = $mobileNumber;
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
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
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
