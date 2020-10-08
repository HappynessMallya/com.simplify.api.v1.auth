<?php
declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\RegisterUserCommand;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserRole;
use App\Domain\Model\User\UserStatus;
use App\Domain\Repository\UserRepository;
use App\Domain\Services\User\PasswordEncoder;

/**
 * Class RegisterUserHandler
 * @package App\Application\ApiUser\CommandHandler
 */
class RegisterUserHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var PasswordEncoder
     */
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository, PasswordEncoder $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function handle(RegisterUserCommand $command): bool
    {
        $user = User::create (
            UserId::generate(),
            $command->getEmail(),
            $command->getUsername(),
            $this->passwordEncoder->hashPassword($command->getPassword()),
            null,
            UserStatus::ACTIVE(),
            UserRole::USER()
        );

        return $this->userRepository->save($user);
    }
}