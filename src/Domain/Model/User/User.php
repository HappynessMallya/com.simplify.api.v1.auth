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

    /** @var string */
    protected string $firstName;

    /** @var string */
    protected string $lastName;

    /** @var string|null */
    protected ?string $username;

    /** @var string */
    protected string $email;

    /** @var string|null */
    protected ?string $mobileNumber;

    /** @var bool */
    protected bool $enabled;

    /** @var string|null */
    protected ?string $salt;

    /** @var string */
    protected string $password;

    /** @var DateTime|null */
    protected ?DateTime $lastLogin;

    /** @var string|null Random string sent to the user email address in order to verify it */
    protected ?string $confirmationToken;

    /** @var DateTime|null */
    protected ?DateTime $passwordRequestedAt;

    /** @var array */
    protected array $roles;

    /** @var UserType  */
    protected UserType $userType;

    /** @var UserStatus */
    protected UserStatus $status;

    /** @var DateTime */
    protected DateTime $createdAt;

    /** @var DateTime|null */
    private ?DateTime $updatedAt;

    /**
     * User constructor
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->roles = [];
    }

    public static function create(
        UserRole $userRole,
        UserId $userId,
        CompanyId $companyId,
        string $email,
        ?string $username,
        ?string $password,
        ?string $salt,
        UserStatus $userStatus,
        UserRole $rol,
        UserType $userType,
        string $firstName,
        string $lastName,
        ?string $mobileNumber
    ): self {
        $self = new self();
        $self->userId = $userId;
        $self->companyId = $companyId;
        $self->firstName = $firstName;
        $self->lastName = $lastName;
        $self->username = $username;
        $self->email = $email;
        $self->mobileNumber = $mobileNumber;
        $self->enabled = true;
        $self->salt = $salt;
        $self->password = $password;
        $self->lastLogin = null;
        $self->confirmationToken = null;
        $self->passwordRequestedAt = null;
        $self->roles[] = $userRole->getName();
        $self->userType = $userType;
        $self->status = $userStatus;
        $self->createdAt = new Datetime();

        return $self;
    }

    /**
     * @return UserId
     */
    public function userId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return CompanyId
     */
    public function companyId(): CompanyId
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function username(): ?string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function mobileNumber(): ?string
    {
        return empty($this->mobileNumber) ? null : $this->mobileNumber;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string|null
     */
    public function salt(): ?string
    {
        return $this->salt;
    }

    /**
     * @return string|null
     */
    public function password(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
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
     * @return DateTime
     */
    public function passwordRequestedAt(): DateTime
    {
        return $this->passwordRequestedAt;
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
     * @param UserRole $role
     */
    public function addRole(UserRole $role): void
    {
        if (!in_array($role->toString(), $this->roles, true)) {
            $this->roles[] = $role->toString();
        }
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
     * @return UserType
     */
    public function getUserType(): UserType
    {
        return $this->userType;
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
     * @return DateTime
     */
    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function updatedAt(): ?DateTime
    {
        return empty($this->updatedAt) ? null : $this->updatedAt;
    }

    /**
     * @return void
     */
    public function login(): void
    {
        $this->lastLogin = new DateTime();
    }

    /**
     * @param array $toUpdate
     */
    public function update(array $toUpdate): void
    {
        $notNull = ['firstName', 'lastName', 'username', 'email', 'mobileNumber'];

        foreach ($toUpdate as $attribute => $newValue) {
            if (property_exists(self::class, $attribute)) {
                if (empty($newValue)) {
                    if (in_array($attribute, $notNull)) {
                        continue;
                    }

                    $this->{$attribute} = null;
                } else {
                    $this->{$attribute} = $newValue;
                }
            }
        }
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
     * @return string
     */
    public function __toString(): string
    {
        return $this->email();
    }
}
