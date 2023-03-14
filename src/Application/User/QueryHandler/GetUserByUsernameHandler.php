<?php

declare(strict_types=1);

namespace App\Application\User\QueryHandler;

use App\Application\User\Query\GetUserByUsernameQuery;
use App\Domain\Model\User\User;
use App\Domain\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetUserByUsernameHandler
 * @package App\Application\User\QueryHandler
 */
class GetUserByUsernameHandler
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
     * @param GetUserByUsernameQuery $query
     * @return User|null
     * @throws Exception
     */
    public function handle(GetUserByUsernameQuery $query): ?User
    {
        $user = $this->userRepository->getByUsername($query->getUsername());

        if (empty($user)) {
            throw new Exception(
                'User not found by username: ' . $query->getUsername(),
                Response::HTTP_NOT_FOUND
            );
        }

        return $user;
    }
}
