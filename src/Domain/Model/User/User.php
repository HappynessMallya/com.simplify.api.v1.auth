<?php

declare(strict_types=1);

namespace App\Domain\Model\User;

use App\Domain\Model\Company\CompanyId;
use DateTime;

/**
 * Class User
 * @package App\Domain\Model\User
 */
class User
{
    /**
     * @var UserId
     */
    protected $userId;

    /**
     * @var CompanyId
     */
    protected $companyId;

    /**
     * @var string|null
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string|null
     */
    protected $salt;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var DateTime|null
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string|null
     */
    protected $confirmationToken;

    /**
     * @var DateTime|null
     */
    protected $passwordRequestedAt;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @var UserStatus
     *
     */
    protected $status;

    /**
     * @var DateTime
     */
    protected $createdAt;

    public function __construct()
    {
        $this->enabled = false;
        $this->roles = [];
    }

    public static function create (
        UserId $userId,
        CompanyId $companyId,
        string $email,
        ?string $username,
        string $password,
        ?string $salt,
        UserStatus $userStatus,
        UserRole $rol
    ): self {
        $self = new self();
        $self->userId = $userId;
        $self->companyId = $companyId;
        $self->email = $email;
        $self->enabled = true;
        $self->username = $username;
        $self->password = $password;
        $self->salt = $salt;
        $self->roles[] = $rol->toString();
        $self->status = $userStatus;
        $self->createdAt = new Datetime();
        $self->lastLogin = null;
        $self->confirmationToken = null;
        $self->passwordRequestedAt = null;

        return $self;
    }

    public function login(): void
    {
        $this->lastLogin = new DateTime();
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function resetPasswordRequest(string $token): void
    {
        $this->passwordRequestedAt = new DateTime();
        $this->confirmationToken = $token;
    }

    public function resetPasswordConfirm(string $password, ?string $salt): void
    {
        $this->password = $password;
        $this->salt = $salt;
        $this->passwordRequestedAt = null;
        $this->confirmationToken = null;
        $this->enabled = true;
    }

    public function changePassword(string $password, ?string $salt): void
    {
        $this->password = $password;
        $this->salt = $salt;
        $this->passwordRequestedAt = null;
        $this->confirmationToken = null;
        $this->enabled = true;
    }

    public function changeStatus(UserStatus $newStatus): void
    {
        if ($newStatus->sameValueAs(UserStatus::ACTIVE())) {
            $this->enabled = true;
        }

        $this->status = $newStatus;
    }

    public function suspend(): void
    {
        $this->enabled = false;
        $this->status = UserStatus::SUSPENDED();
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function status(): UserStatus
    {
        if ($this->status instanceof UserStatus) {
            return $this->status;
        }

        return UserStatus::byValue($this->status);
    }

    public function addRole(UserRole $role): void
    {
        if (!in_array($role->toString(), $this->roles, true)) {
            $this->roles[] = $role->toString();
        }
    }

    public function roles(): array
    {
        $roles = [];

        foreach (array_unique($this->roles) as $rol) {
                $roles[] = UserRole::byName($rol)->getName();
        }

        return $roles;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return (string) $this->email();
    }

    public function companyId(): CompanyId
    {
        return $this->companyId;
    }

    public function username(): ?string
    {
        return $this->username;
    }

    public function salt(): ?string
    {
        return $this->salt;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function lastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    public function confirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    protected function hasRole(UserRole $role): bool
    {
        if (in_array($role->toString(), $this->roles, true)) {
            return true;
        }

        return false;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ADMIN());
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserRole::SUPER_ADMIN());
    }

    public function removeRole(UserRole $role): void
    {
        if (false !== $key = array_search($role->toString(), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    public function passwordRequestedAt(): DateTime
    {
        return $this->passwordRequestedAt;
    }
}

