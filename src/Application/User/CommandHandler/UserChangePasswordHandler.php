<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\UserChangePasswordCommand;
use App\Domain\Model\User\UserStatus;
use App\Domain\Repository\UserRepository;
use App\Domain\Services\User\PasswordEncoder;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserChangePasswordHandler
 * @package App\Application\User\CommandHandler
 */
class UserChangePasswordHandler
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

        $user = $this->userRepository->get($userEntity->userId());

        $userEntity->setPassword($command->getPassword());
        $passwordEncoded = $this->passwordEncoder->hashPassword($userEntity);

        $user->setPassword($passwordEncoded);

        if ($user->status()->sameValueAs(UserStatus::CHANGE_PASSWORD())) {
            $user->changeStatus(UserStatus::ACTIVE());
        }

        $user->update(
            [
                'updatedAt' => new DateTime('now'),
            ]
        );


        return $this->userRepository->save($user);
    }
}
