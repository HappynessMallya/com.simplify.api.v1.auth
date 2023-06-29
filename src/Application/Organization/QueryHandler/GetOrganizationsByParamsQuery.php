<?php

declare(strict_types=1);

namespace App\Application\Organization\QueryHandler;

/**
 * Class GetOrganizationsByParamsQuery
 * @package App\Application\Organization\QueryHandler
 */
class GetOrganizationsByParamsQuery
{
    /** @var string */
    private string $name;

    /** @var string */
    private string $ownerName;

    /** @var string */
    private string $ownerEmail;

    /** @var string */
    private string $ownerPhoneNumber;

    /** @var string */
    private string $status;

    /**
     * @param string $name
     * @param string $ownerName
     * @param string $ownerEmail
     * @param string $ownerPhoneNumber
     * @param string $status
     */
    public function __construct(
        string $name,
        string $ownerName,
        string $ownerEmail,
        string $ownerPhoneNumber,
        string $status
    ) {
        $this->name = $name;
        $this->ownerName = $ownerName;
        $this->ownerEmail = $ownerEmail;
        $this->ownerPhoneNumber = $ownerPhoneNumber;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    /**
     * @return string
     */
    public function getOwnerEmail(): string
    {
        return $this->ownerEmail;
    }

    /**
     * @return string
     */
    public function getOwnerPhoneNumber(): string
    {
        return $this->ownerPhoneNumber;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
