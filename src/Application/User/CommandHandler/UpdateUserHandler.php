<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\UpdateUserCommand;
use App\Domain\Model\User\UserId;
use App\Domain\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UpdateUserHandler
 * @package App\Application\User\CommandHandler
 */
class UpdateUserHandler
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
     * @param UpdateUserCommand $command
     * @return bool|null
     * @throws Exception
     */
    public function handle(UpdateUserCommand $command): ?bool
    {
        $userId = UserId::fromString($command->getUserId());
        $user = $this->userRepository->get($userId);

        if (empty($user)) {
            throw new Exception('User not found by ID: ' . $userId, Response::HTTP_NOT_FOUND);
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
