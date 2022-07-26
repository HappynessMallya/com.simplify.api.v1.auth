<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\UserChangePasswordCommand;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserStatus;
use App\Domain\Repository\UserRepository;
use App\Domain\Services\User\PasswordEncoder;
use App\Infrastructure\Symfony\Security\UserEntity;

/**
 * Class UserChangePasswordHandler
 * @package App\Application\User\CommandHandler
 */
class UserChangePasswordHandler
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var PasswordEncoder
     */
    private PasswordEncoder $passwordEncoder;

    public function __construct(UserRepository $userRepository, PasswordEncoder $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param UserChangePasswordCommand $command
     * @return bool
     */
    public function handle(UserChangePasswordCommand $command): bool
    {
        /** @var UserEntity $entity */
        $entity = $this->userRepository->findOneBy(['email' => $command->getUsername()]);
        /** @var User $user */
        $user = $this->userRepository->find($entity->userId());

        $entity->setPassword($command->getPassword());
        $passwordEncoded = $this->passwordEncoder->hashPassword($entity);
        $user->setPassword($passwordEncoded);
        $user->changeStatus(UserStatus::byValue($command->getStatus()));

        return $this->userRepository->save($user);
    }
}
