<?php

declare(strict_types=1);

namespace App\Application\User\QueryHandler;

use App\Application\User\Query\GetOperatorsByOrganizationQuery;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetOperatorsByOrganizationIdHandler
 * @package App\Application\User\QueryHandler
 */
class GetOperatorsByOrganizationHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var CompanyByUserRepository */
    private CompanyByUserRepository $companyByUserRepository;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /**
     * @param LoggerInterface $logger
     * @param CompanyRepository $companyRepository
     * @param CompanyByUserRepository $companyByUserRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        LoggerInterface $logger,
        CompanyRepository $companyRepository,
        CompanyByUserRepository $companyByUserRepository,
        UserRepository $userRepository
    ) {
        $this->logger = $logger;
        $this->companyRepository = $companyRepository;
        $this->companyByUserRepository = $companyByUserRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param GetOperatorsByOrganizationQuery $query
     * @return array
     * @throws Exception
     */
    public function __invoke(GetOperatorsByOrganizationQuery $query): array
    {
        $organizationId = OrganizationId::fromString($query->getOrganizationId());
        $userType = UserType::byName($query->getUserType());

        if ($userType->sameValueAs(UserType::TYPE_OWNER())) {
            $companies = $this->companyRepository->getCompaniesByOrganizationId($organizationId);
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

        if (empty($companies)) {
            $this->logger->critical(
                'Operators could not be found by organization',
                [
                    'user_id' => $organizationId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Operators could not be found by organization',
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
                'userId' => UserId::fromString($index),
                'userType' => UserType::TYPE_OPERATOR(),
            ];
            $userEntity = $this->userRepository->findOneBy($criteria);
            if (!empty($userEntity)) {
                $operator = $this->userRepository->get($userEntity->userId());

                $companies = [];
                foreach ($user as $item) {
                    $company = $this->companyRepository->get(CompanyId::fromString($item['company_id']));

                    $companies[] = [
                        'company_id' => $company->companyId()->toString(),
                        'name' => $company->name(),
                        'tin' => $company->tin(),
                        'email' => $company->email(),
                        'serial' => $company->serial(),
                        'status' => $company->companyStatus(),
                    ];
                }

                $operators[] = [
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

        return $operators;
    }
}
