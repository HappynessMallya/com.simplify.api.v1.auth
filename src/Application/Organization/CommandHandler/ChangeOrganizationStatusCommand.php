<?php

declare(strict_types=1);

namespace App\Application\Organization\CommandHandler;

/**
 * Class ChangeOrganizationStatusCommand
 * @package App\Application\Organization\CommandHandler
 */
class ChangeOrganizationStatusCommand
{
    /** @var string */
    private string $organizationId;

    /** @var string */
    private string $status;

    /** @var string|null */
    private ?string $userType;

    /**
     * @return string
     */
    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    /**
     * @param string $organizationId
     */
    public function setOrganizationId(string $organizationId): void
    {
        $this->organizationId = $organizationId;
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
     * @return string|null
     */
    public function getUserType(): ?string
    {
        return empty($this->userType) ? null : $this->userType;
    }

    /**
     * @param string|null $userType
     */
    public function setUserType(?string $userType): void
    {
        $this->userType = $userType;
    }
}
