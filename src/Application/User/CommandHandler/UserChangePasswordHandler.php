<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\UserChangePasswordCommand;
use App\Domain\Model\User\UserStatus;
use App\Domain\Repository\UserRepository;
use App\Domain\Services\User\PasswordEncoder;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserChangePasswordHandler
 * @package App\Application\User\CommandHandler
 */
class UserChangePasswordHandler
{
    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var PasswordEncoder */
    private PasswordEncoder $passwordEncoder;

    /**
     * @param UserRepository $userRepository
     * @param PasswordEncoder $passwordEncoder
     */
    public function __construct(UserRepository $userRepository, PasswordEncoder $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param UserChangePasswordCommand $command
     * @return bool
     * @throws Exception
     */
    public function handle(UserChangePasswordCommand $command): bool
    {
        $userEntity = $this->userRepository->findOneBy(
            [
                'email' => $command->getUsername(),
            ]
        );

        if (empty($userEntity)) {
            throw new Exception(
                'User not found by email: ' . $command->getUsername(),
                Response::HTTP_NOT_FOUND
            );
        }

        $user = $this->userRepository->get($userEntity->userId());

        $userEntity->setPassword($command->getPassword());
        $passwordEncoded = $this->passwordEncoder->hashPassword($userEntity);

        $user->setPassword($passwordEncoded);
        $user->changeStatus(UserStatus::CHANGE_PASSWORD());

        return $this->userRepository->save($user);
    }
}
