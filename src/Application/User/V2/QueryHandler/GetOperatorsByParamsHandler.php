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
 * Class GetOperatorsByParamsHandler
 * @package App\Application\User\V2\QueryHandler
 */
class GetOperatorsByParamsHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var UserRepository  */
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
     * @param GetOperatorsByParamsQuery $query
     * @return array|null
     * @throws Exception
     */
    public function __invoke(GetOperatorsByParamsQuery $query): array
    {
        $userId = UserId::fromString($query->getUserId());
        $userType = UserType::byName($query->getUserType());

        if ($userType->sameValueAs(UserType::TYPE_OWNER())) {
            $criteria = [
                'userType' => UserType::TYPE_OPERATOR(),
            ];

            if (!empty($query->getFirstName())) {
                $criteria['firstName'] = trim($query->getFirstName());
            }

            if (!empty($query->getLastName())) {
                $criteria['lastName'] = trim($query->getLastName());
            }

            if (!empty($query->getEmail())) {
                $criteria['email'] = trim($query->getEmail());
            }

            if (!empty($query->getMobileNumber())) {
                $criteria['mobileNumber'] = trim($query->getMobileNumber());
            }

            if ($query->getStatus() !== 'ALL') {
                $criteria['status'] = UserStatus::byValue(trim($query->getStatus()));
            }

            $operatorsByCriteria = $this->userRepository->findByCriteria($criteria);
        } else {
            $this->logger->critical(
                'User is not an owner',
                [
                    'user_id' => $userId->toString(),
                    'user_type' => $userType->getValue(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User is not an owner: ' . $userType->getValue(),
                Response::HTTP_BAD_REQUEST
            );
        }

        $operators = [];

        if (!empty($operatorsByCriteria)) {
            foreach ($operatorsByCriteria as $operator) {
                $operators[] = [
                    'userId' => $operator->userId()->toString(),
                    'firstName' => $operator->firstName(),
                    'lastName' => $operator->lastName(),
                    'email' => $operator->email(),
                    'mobileNumber' => $operator->mobileNumber() ?? '',
                    'status' => $operator->status()->getValue(),
                    'createdAt' => $operator->createdAt()->format(DATE_ATOM),
                ];
            }
        }

        return $operators;
    }
}
