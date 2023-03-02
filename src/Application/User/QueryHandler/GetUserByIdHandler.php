<?php

declare(strict_types=1);

namespace App\Application\User\QueryHandler;

use App\Application\User\Query\GetUserByIdQuery;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Domain\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetUserByIdHandler
 * @package App\Application\User\QueryHandler
 */
class GetUserByIdHandler
{
    /** @var UserRepository */
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param GetUserByIdQuery $command
     * @return User|null
     * @throws Exception
     */
    public function handle(GetUserByIdQuery $command): ?User
    {
        $userId = UserId::fromString($command->getUserId());
        $user = $this->userRepository->get($userId);

        if (empty($user)) {
            throw new Exception('User not found by ID: ' . $userId, Response::HTTP_NOT_FOUND);
        }

        return $user;
    }
}
