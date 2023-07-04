<?php

declare(strict_types=1);

namespace App\Application\User\QueryHandler;

use App\Application\User\Query\GetOperatorsByParamsQuery;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserStatus;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\CompanyRepository;
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

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var CompanyByUserRepository */
    private CompanyByUserRepository $companyByUserRepository;

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param CompanyByUserRepository $companyByUserRepository
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        CompanyByUserRepository $companyByUserRepository
    ) {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->companyByUserRepository = $companyByUserRepository;
    }

    /**
     * @param GetOperatorsByParamsQuery $query
     * @return array|null
     * @throws Exception
     */
    public function __invoke(GetOperatorsByParamsQuery $query): array
    {
        $organizationId = OrganizationId::fromString($query->getOrganizationId());
        $userId = UserId::fromString($query->getUserId());
        $userType = UserType::byName($query->getUserType());

        if ($userType->sameValueAs(UserType::TYPE_OWNER())) {
            $companies = $this->companyRepository->getCompaniesByOrganizationId($organizationId);
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

        if (empty($companies)) {
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

        $users = [];
        foreach ($companies as $company) {
            $users[] = $this->companyByUserRepository->getOperatorsByCompany($company->companyId());
        }

        $usersBelongToOrganization = [];
        foreach ($users as $user) {
            foreach ($user as $item) {
                $usersBelongToOrganization[$item['user_id']][] = [
                    'company_id' => $item['company_id'],
                    'status' => $item['status'],
                ];
            }
        }

        $operators = [];
        foreach ($usersBelongToOrganization as $index => $user) {
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

            $operators = $this->userRepository->findByCriteria($criteria);
        }

        $operatorsByParams = [];

        if (!empty($operators)) {
            foreach ($operators as $operator) {
                if (array_key_exists($operator->userId()->toString(), $usersBelongToOrganization)) {
                    $companyOfOperator = $usersBelongToOrganization[$operator->userId()->toString()];

                    $companies = [];
                    foreach ($companyOfOperator as $company) {
                        $company = $this->companyRepository->get(CompanyId::fromString($company['company_id']));

                        $companies[] = [
                            'company_id' => $company->companyId()->toString(),
                            'name' => $company->name(),
                            'tin' => $company->tin(),
                            'email' => $company->email(),
                            'serial' => $company->serial(),
                            'status' => $company->companyStatus(),
                        ];
                    }

                    $operatorsByParams[] = [
                        'userId' => $operator->userId()->toString(),
                        'firstName' => $operator->firstName(),
                        'lastName' => $operator->lastName(),
                        'email' => $operator->email(),
                        'mobileNumber' => $operator->mobileNumber(),
                        'userType' => $operator->getUserType()->getValue(),
                        'companies' => $companies,
                        'createdAt' => $operator->createdAt()->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        return $operatorsByParams;
    }
}
