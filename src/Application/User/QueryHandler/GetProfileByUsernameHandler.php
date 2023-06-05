<?php

declare(strict_types=1);

namespace App\Application\User\QueryHandler;

use App\Application\User\Query\GetProfileByUsernameQuery;
use App\Domain\Model\User\User;
use App\Domain\Repository\UserRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetProfileByUsernameHandler
 * @package App\Application\User\QueryHandler
 */
class GetProfileByUsernameHandler
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
     * @param GetProfileByUsernameQuery $query
     * @return User|null
     * @throws Exception
     */
    public function handle(GetProfileByUsernameQuery $query): ?User
    {
        $user = $this->userRepository->getByUsername($query->getUsername());

        if (empty($user)) {
            $this->logger->critical(
                'User not found by username',
                [
                    'username' => $query->getUsername(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User not found by username: ' . $query->getUsername(),
                Response::HTTP_NOT_FOUND
            );
        }

        return $user;
    }
}
