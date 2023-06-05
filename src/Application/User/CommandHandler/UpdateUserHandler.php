<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\UpdateUserCommand;
use App\Domain\Repository\UserRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UpdateUserHandler
 * @package App\Application\User\CommandHandler
 */
class UpdateUserHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository
    ) {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
    }

    /**
     * @param UpdateUserCommand $command
     * @return bool|null
     * @throws Exception
     */
    public function handle(UpdateUserCommand $command): ?bool
    {
        $user = $this->userRepository->getByUsername($command->getUsername());

        if (empty($user)) {
            $this->logger->critical(
                'User not found by username',
                [
                    'username' => $command->getUsername(),
                    'email' => $command->getEmail(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User not found by username: ' . $command->getUsername(),
                Response::HTTP_NOT_FOUND
            );
        }

        $user->update(
            [
                'firstName' => $command->getFirstName(),
                'lastName' => $command->getLastName(),
                'username' => $command->getEmail(),
                'email' => $command->getEmail(),
                'mobileNumber' => $command->getMobileNumber(),
            ]
        );

        return $this->userRepository->save($user);
    }
}
