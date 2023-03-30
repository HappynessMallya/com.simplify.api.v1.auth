<?php

declare(strict_types=1);

namespace App\Application\User\V2\QueryHandler;

use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\UserRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetUsersByOrganizationHandler
 * @package App\Application\User\V2\QueryHandler
 */
class GetUsersByOrganizationHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var CompanyByUserRepository  */
    private CompanyByUserRepository $companyByUserRepository;

    /** @var UserRepository  */
    private UserRepository $userRepository;

    /**
     * @param LoggerInterface $logger
     * @param CompanyByUserRepository $companyByUserRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        LoggerInterface $logger,
        CompanyByUserRepository $companyByUserRepository,
        UserRepository $userRepository
    ) {
        $this->logger = $logger;
        $this->companyByUserRepository = $companyByUserRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param GetUsersByOrganizationQuery $query
     * @return array
     * @throws Exception
     */
    public function __invoke(GetUsersByOrganizationQuery $query): array
    {
        $organizationId = OrganizationId::fromString($query->getOrganizationId());
        $userType = UserType::byName($query->getUserType());

        if ($userType->sameValueAs(UserType::TYPE_OWNER())) {
            $operatorsByOrganization = $this->companyByUserRepository->getOperatorsByOrganization($organizationId);
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

        if (empty($operatorsByOrganization)) {
            $this->logger->critical(
                'Operators not found by organization',
                [
                    'user_id' => $organizationId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Operators not found by organization: ' . $organizationId->toString(),
                Response::HTTP_NOT_FOUND
            );
        }

        $operators = [];

        foreach ($operatorsByOrganization as $operator) {
            $operatorFound = $this->userRepository->get(UserId::fromString($operator['user_id']));

            $operators[] = [
                'userId' => $operatorFound->userId()->toString(),
                'companyId' => $operatorFound->companyId()->toString(),
                'firstName' => $operatorFound->firstName(),
                'lastName' => $operatorFound->lastName(),
                'email' => $operatorFound->email(),
                'mobileNumber' => $operatorFound->mobileNumber(),
                'createdAt' => $operatorFound->createdAt()->format(DATE_ATOM),
                'status' => $operatorFound->status()->getValue(),
            ];
        }

        return $operators;
    }
}
