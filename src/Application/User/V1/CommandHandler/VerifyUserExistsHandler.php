<?php

declare(strict_types=1);

namespace App\Application\User\V1\CommandHandler;

use App\Application\User\V1\Command\VerifyUserExistsCommand;
use App\Domain\Repository\UserRepository;
use Psr\Log\LoggerInterface;

/**
 * Class VerifyUserExistsHandler
 * @package App\Application\User\V1\CommandHandler
 */
class VerifyUserExistsHandler
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
     * @param VerifyUserExistsCommand $command
     * @return bool
     */
    public function __invoke(VerifyUserExistsCommand $command): bool
    {
        $criteria = [
            'email' => $command->getUsername(),
        ];

        $user = $this->userRepository->findOneBy($criteria);

        if (empty($user)) {
            $this->logger->critical(
                'User could not be found',
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
