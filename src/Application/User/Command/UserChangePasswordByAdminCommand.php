<?php

declare(strict_types=1);

namespace App\Application\User\Command;

use App\Domain\Model\User\UserStatus;

/**
 * Class UserChangePasswordByAdminCommand
 * @package App\Application\User\Command
 */
class UserChangePasswordByAdminCommand
{
    /** @var string|null */
    private ?string $companyId;

    /** @var string */
    private string $username;

    /** @var string */
    private string $currentPassword;

    /** @var string */
    private string $newPassword;

    /** @var string */
    private string $status;

    /** @var string|null */
    private ?string $salt;

    /**
     * @return string|null
     */
    public function getCompanyId(): ?string
    {
        return empty($this->companyId) ? null : $this->companyId;
    }

    /**
     * @param string $companyId
     */
    public function setCompanyId(string $companyId): void
    {
        $this->companyId = $companyId;
    }

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
     * @return string
     */
    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    /**
     * @param string $currentPassword
     */
    public function setCurrentPassword(string $currentPassword): void
    {
        $this->currentPassword = $currentPassword;
    }

    /**
     * @return string
     */
    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     */
    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        if (empty($this->status)) {
            $this->status = UserStatus::ACTIVE;
        }

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
    public function getSalt(): ?string
    {
        return empty($this->salt) ? null : $this->salt;
    }

    /**
     * @param string|null $salt
     */
    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }
}
