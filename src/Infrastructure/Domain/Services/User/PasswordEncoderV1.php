<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Services\User;

use App\Domain\Model\User\User;
use App\Domain\Services\User\PasswordEncoder;
use App\Infrastructure\Symfony\Security\UserEntity;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PasswordEncoderV1
 * @package App\Infrastructure\Domain\Services\ApiUser
 */
class PasswordEncoderV1 implements PasswordEncoder
{
    /** @var string|null */
    private ?string $salt;

    /** @var UserPasswordEncoderInterface */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->salt = null;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return empty($this->salt) ? null : $this->salt;
    }

    /**
     * @param User $user
     * @return string|null
     */
    public function hashPassword(User $user): ?string
    {
        $user = new UserEntity(
            $user->userId(),
            $user->companyId(),
            $user->email(),
            $user->username(),
            $user->password(),
            $user->salt(),
            $user->status(),
            $user->roles()
        );

        return $this->passwordEncoder->encodePassword($user, $user->getPassword());
    }

    /**
     * @param UserInterface $user
     * @param string $raw
     * @return bool
     */
    public function isPasswordValid(UserInterface $user, string $raw): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $raw);
    }
}
