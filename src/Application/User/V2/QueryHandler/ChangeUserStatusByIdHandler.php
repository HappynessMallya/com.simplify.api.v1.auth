<?php

declare(strict_types=1);

namespace App\Application\User\V2\QueryHandler;

use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserStatus;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\UserRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChangeUserStatusByIdHandler
 * @package App\Application\User\V2\QueryHandler
 */
class ChangeUserStatusByIdHandler
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
     * @param ChangeUserStatusByIdQuery $query
     * @return bool
     * @throws Exception
     */
    public function __invoke(ChangeUserStatusByIdQuery $query): bool
    {
        $userId = UserId::fromString($query->getUserId());
        $userType = UserType::byName($query->getUserType());
        $newStatus = UserStatus::byName($query->getNewStatus());

        if ($userType->sameValueAs(UserType::TYPE_OWNER()) || $userType->sameValueAs(UserType::TYPE_ADMIN())) {
            $user = $this->userRepository->get($userId);
        } else {
            $this->logger->critical(
                'User who is making the change is neither owner nor admin',
                [
                    'user_type' => $userType->getValue(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User who is making the change is neither owner nor admin: ' . $userType->getValue(),
                Response::HTTP_BAD_REQUEST
            );
        }

        if (empty($user)) {
            $this->logger->critical(
                'User not found by ID',
                [
                    'user_id' => $userId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User not found by ID: ' . $userId->toString(),
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
            ]
        );

        return $this->userRepository->save($user);
    }
}
