<?php

declare(strict_types=1);

namespace App\Application\User\V1\QueryHandler;

use App\Application\User\V1\Query\GetUsersQuery;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetUsersHandler
 * @package App\Application\User\V1\QueryHandler
 */
class GetUsersHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var UserRepository */
    private UserRepository $userRepository;

    private CompanyRepository $companyRepository;

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository,
        CompanyRepository $companyRepository
    ) {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param GetUsersQuery $query
     * @return array
     * @throws Exception
     */
    public function __invoke(GetUsersQuery $query): array
    {
        $userType = UserType::byName($query->getUserType());

        if ($userType->sameValueAs(UserType::TYPE_ADMIN())) {
            $usersFound = $this->userRepository->findByCriteria([]);
        } else {
            $this->logger->critical(
                'User is not an admin',
                [
                    'user_type' => $userType->getValue(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User is not an admin: ' . $userType->getValue(),
                Response::HTTP_BAD_REQUEST
            );
        }

        if (empty($usersFound)) {
            $this->logger->critical(
                'Users could not be found',
                [
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Users could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        $users = [];

        /** @var User $user */
        foreach ($usersFound as $user) {
            $company = $this->companyRepository->get($user->companyId());
            $users[] = [
                'id' => $user->userId()->toString(),
                'companyId' => $user->companyId()->toString(),
                'company' => $company->name(),
                'firstName' => $user->firstName(),
                'lastName' => $user->lastName(),
                'username' => $user->username() ?? '',
                'email' => $user->email(),
                'mobileNumber' => $user->mobileNumber() ?? '',
                'userType' => $user->getUserType()->getValue(),
                'status' => $user->status()->getValue(),
                'enable' => $user->isEnabled(),
                'createdAt' => $user->createdAt()->format('Y-m-d H:i:s'),
            ];
        }

        return $users;
    }
}
