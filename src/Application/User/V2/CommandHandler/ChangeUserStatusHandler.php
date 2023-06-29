<?php

declare(strict_types=1);

namespace App\Application\User\V2\CommandHandler;

use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserStatus;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\UserRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChangeUserStatusHandler
 * @package App\Application\User\V2\CommandHandler
 */
class ChangeUserStatusHandler
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
     * @param ChangeUserStatusCommand $command
     * @return bool
     * @throws Exception
     */
    public function __invoke(ChangeUserStatusCommand $command): bool
    {
        $userId = UserId::fromString($command->getUserId());
        $userTypeWhoChangeStatus = UserType::byName($command->getUserType());
        $newStatus = UserStatus::byName($command->getStatus());

        if (
            $userTypeWhoChangeStatus->sameValueAs(UserType::TYPE_OWNER()) ||
            $userTypeWhoChangeStatus->sameValueAs(UserType::TYPE_ADMIN())
        ) {
            $user = $this->userRepository->get($userId);
        } else {
            $this->logger->critical(
                'User who is making the change is neither owner nor admin',
                [
                    'user_type' => $userTypeWhoChangeStatus->getValue(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User who is making the change is neither owner nor admin: ' . $userTypeWhoChangeStatus->getValue(),
                Response::HTTP_BAD_REQUEST
            );
        }

        if (empty($user)) {
            $this->logger->critical(
                'User could not be found',
                [
                    'user_id' => $userId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        if ($user->status()->is($newStatus)) {
            $this->logger->critical(
                'User status is the same one',
                [
                    'organization_status' => $user->status()->toString(),
                    'new_status' => $newStatus->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User status is the same one: ' . $user->status()->toString(),
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->update(
            [
                'status' => $newStatus,
                'updatedAt' => new DateTime('now'),
            ]
        );

        return $this->userRepository->save($user);
    }
}
