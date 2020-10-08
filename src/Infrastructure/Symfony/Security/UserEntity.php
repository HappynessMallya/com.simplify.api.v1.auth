<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Security;

use App\Domain\Model\User\UserRole;
use App\Domain\Model\User\UserStatus;
use App\Infrastructure\Repository\DoctrineUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ApiUser
 * @package App\Infrastructure\Symfony\Security
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass=DoctrineUserRepository::class)
 */
class UserEntity implements UserInterface
{
    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $userId;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $salt;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="UserStatus")
     *
     */
    private $status;

    public function __construct(
        string $userId,
        ?string $username,
        string $password,
        ?string $salt,
        string $email,
        array $roles,
        bool $enabled,
        UserStatus $status
    ) {
        $this->userId = $userId;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->email = $email;
        $this->roles = $roles;
        $this->enabled = $enabled;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
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
    public function getPassword(): ?string
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
     * @return string
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = [];

        foreach (array_unique($this->roles) as $value) {
            $roles[] = UserRole::byName($value);
        }

        return $roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    public function eraseCredentials()
    {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getEmail();
    }
}