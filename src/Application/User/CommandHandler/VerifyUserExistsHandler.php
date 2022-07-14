<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\VerifyUserExistsCommand;
use App\Domain\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class VerifyUserExistsHandler
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    private UserRepository $userRepository;

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     */
    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
    }

    public function __invoke(VerifyUserExistsCommand $command): bool
    {
        $criteria = [
            'email' => $command->getUsername()
        ];

        $user = $this->userRepository->findOneBy($criteria);

        if (empty($user)) {
            $this->logger->critical(
                'The user could not be found',
                [
                    'username' => $command->getUsername(),
                    'method' => __METHOD__,
                ]
            );

            return false;
        }

        return true;
    }
}
