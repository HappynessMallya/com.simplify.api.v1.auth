<?php
declare(strict_types=1);

namespace App\Infrastructure\Domain\Services\User;

use App\Domain\Services\User\PasswordEncoder;
use App\Infrastructure\Symfony\Security\UserEntity;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class PasswordEncoderV1
 * @package App\Infrastructure\Domain\Services\ApiUser
 */
final class PasswordEncoderV1 implements PasswordEncoder
{
    /**
     * @var null|string
     */
    private $salt;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->salt = null;
        $this->encoder = $passwordEncoder;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string $plainPassword
     * @return string|null
     */
    public function hashPassword(string $plainPassword): ?string
    {
        if (empty($plainPassword)) {
            return null;
        }

        $user = new UserEntity();
        $user->setPassword($plainPassword);

        return $this->encoder->encodePassword($user, $plainPassword);
    }
}
