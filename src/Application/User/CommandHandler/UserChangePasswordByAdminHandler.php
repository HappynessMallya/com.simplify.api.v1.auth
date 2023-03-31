<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\UserChangePasswordByAdminCommand;
use App\Domain\Model\User\UserStatus;
use App\Domain\Repository\UserRepository;
use App\Domain\Services\User\PasswordEncoder;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserChangePasswordByAdminHandler
 * @package App\Application\User\CommandHandler
 */
class UserChangePasswordByAdminHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var PasswordEncoder */
    private PasswordEncoder $passwordEncoder;

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     * @param PasswordEncoder $passwordEncoder
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository,
        PasswordEncoder $passwordEncoder
    ) {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param UserChangePasswordByAdminCommand $command
     * @return bool
     * @throws Exception
     */
    public function handle(UserChangePasswordByAdminCommand $command): bool
    {
        $userEntity = $this->userRepository->findOneBy(
            [
                'email' => $command->getUsername(),
            ]
        );

        if (empty($userEntity)) {
            $this->logger->critical(
                'User not found by email',
                [
                    'username' => $command->getUsername(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User not found by email: ' . $command->getUsername(),
                Response::HTTP_NOT_FOUND
            );
        }

        $isCurrentPassword = $this->passwordEncoder->isPasswordValid($userEntity, $command->getCurrentPassword());

        if ($isCurrentPassword) {
            $user = $this->userRepository->get($userEntity->userId());

            $userEntity->setPassword($command->getNewPassword());
            $passwordEncoded = $this->passwordEncoder->hashPassword($userEntity);

            $user->setPassword($passwordEncoded);
            $user->changeStatus(UserStatus::ACTIVE());

            return $this->userRepository->save($user);
        }

        return false;
    }
}
