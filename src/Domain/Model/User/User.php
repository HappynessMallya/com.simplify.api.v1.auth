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
    /** @var UserId */
    protected UserId $userId;

    /** @var CompanyId */
    protected CompanyId $companyId;

    /** @var string|null */
    protected ?string $username;

    /** @var string */
    protected string $email;

    /** @var bool */
    protected bool $enabled;

    /** @var string|null */
    protected ?string $salt;

    /** @var string */
    protected string $password;

    /** @var DateTime|null */
    protected ?DateTime $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     * @var string|null
     */
    protected ?string $confirmationToken;

    /** @var DateTime|null */
    protected ?DateTime $passwordRequestedAt;

    /** @var array */
    protected array $roles;

    /** @var UserStatus */
    protected UserStatus $status;

    /** @var DateTime */
    protected DateTime $createdAt;

    /**
     * User constructor
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->roles = [];
    }

    public static function create(
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

    /**
     * @return void
     */
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

    /**
     * @param string $token
     */
    public function resetPasswordRequest(string $token): void
    {
        $this->passwordRequestedAt = new DateTime();
        $this->confirmationToken = $token;
    }

    /**
     * @param string $password
     * @param string|null $salt
     */
    public function resetPasswordConfirm(string $password, ?string $salt): void
    {
        $this->password = $password;
        $this->salt = $salt;
        $this->passwordRequestedAt = null;
        $this->confirmationToken = null;
        $this->enabled = true;
    }

    /**
     * @param string $password
     * @param string|null $salt
     */
    public function changePassword(string $password, ?string $salt): void
    {
        $this->password = $password;
        $this->salt = $salt;
        $this->passwordRequestedAt = null;
        $this->confirmationToken = null;
        $this->enabled = true;
    }

    /**
     * @param UserStatus $newStatus
     */
    public function changeStatus(UserStatus $newStatus): void
    {
        if ($newStatus->sameValueAs(UserStatus::ACTIVE())) {
            $this->enabled = true;
        }

        $this->status = $newStatus;
    }

    /**
     * @return void
     */
    public function suspend(): void
    {
        $this->enabled = false;
        $this->status = UserStatus::SUSPENDED();
    }

    /**
     * @return UserId
     */
    public function userId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return UserStatus
     */
    public function status(): UserStatus
    {
        if ($this->status instanceof UserStatus) {
            return $this->status;
        }

        return UserStatus::byValue($this->status);
    }

    /**
     * @param UserRole $role
     */
    public function addRole(UserRole $role): void
    {
        if (!in_array($role->toString(), $this->roles, true)) {
            $this->roles[] = $role->toString();
        }
    }

    /**
     * @return array
     */
    public function roles(): array
    {
        $roles = [];

        foreach (array_unique($this->roles) as $rol) {
                $roles[] = UserRole::byName($rol)->getName();
        }

        return $roles;
    }

    /**
     * @return DateTime
     */
    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->email();
    }

    /**
     * @return CompanyId
     */
    public function companyId(): CompanyId
    {
        return $this->companyId;
    }

    /**
     * @return string|null
     */
    public function username(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function salt(): ?string
    {
        return $this->salt;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->password;
    }

    /**
     * @return DateTime|null
     */
    public function lastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @return string|null
     */
    public function confirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param UserRole $role
     * @return bool
     */
    protected function hasRole(UserRole $role): bool
    {
        if (in_array($role->toString(), $this->roles, true)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ADMIN());
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserRole::SUPER_ADMIN());
    }

    /**
     * @param UserRole $role
     */
    public function removeRole(UserRole $role): void
    {
        if (false !== $key = array_search($role->toString(), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    /**
     * @return DateTime
     */
    public function passwordRequestedAt(): DateTime
    {
        return $this->passwordRequestedAt;
    }
}
