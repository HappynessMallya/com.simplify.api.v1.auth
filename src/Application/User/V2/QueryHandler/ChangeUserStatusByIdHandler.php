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
        $operatorId = UserId::fromString($query->getOperatorId());
        $userType = UserType::byName($query->getUserType());
        $newStatus = UserStatus::byName($query->getNewStatus());

        if ($userType->sameValueAs(UserType::TYPE_OWNER())) {
            $operator = $this->userRepository->get($operatorId);
        } else {
            $this->logger->critical(
                'User is not an owner',
                [
                    'user_type' => $userType->getValue(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User is not an owner: ' . $userType->getValue(),
                Response::HTTP_BAD_REQUEST
            );
        }

        if (empty($operator)) {
            $this->logger->critical(
                'Operator not found by ID',
                [
                    'operator_id' => $operatorId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Operator not found by ID: ' . $operatorId->toString(),
                Response::HTTP_NOT_FOUND
            );
        }

        if ($operator->status()->is($newStatus)) {
            $this->logger->critical(
                'Operator status is the same one',
                [
                    'organization_status' => $operator->status()->toString(),
                    'new_status' => $newStatus->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Operator status is the same one: ' . $operator->status()->toString(),
                Response::HTTP_BAD_REQUEST
            );
        }

        $operator->update(
            [
                'status' => $newStatus,
            ]
        );

        return $this->userRepository->save($operator);
    }
}
