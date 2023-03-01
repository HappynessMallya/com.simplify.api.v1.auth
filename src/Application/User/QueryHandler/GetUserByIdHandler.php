<?php

declare(strict_types=1);

namespace App\Application\User\QueryHandler;

use App\Application\User\Query\GetUserByIdQuery;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Domain\Repository\UserRepository;

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
     */
    public function handle(GetUserByIdQuery $command): ?User
    {
        return $this->userRepository->get(UserId::fromString($command->getUserId()));
    }
}
