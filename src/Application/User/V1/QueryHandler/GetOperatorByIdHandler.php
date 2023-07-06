<?php

declare(strict_types=1);

namespace App\Application\User\V1\QueryHandler;

use App\Application\User\V1\Query\GetOperatorByIdQuery;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetOperatorByIdHandler
 * @package App\Application\User\V1\QueryHandler
 */
class GetOperatorByIdHandler
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
     * @param GetOperatorByIdQuery $query
     * @return array
     * @throws Exception
     */
    public function __invoke(GetOperatorByIdQuery $query): array
    {
        $operatorId = UserId::fromString($query->getOperatorId());
        $userType = UserType::byName($query->getUserType());

        if ($userType->sameValueAs(UserType::TYPE_OWNER()) || $userType->sameValueAs(UserType::TYPE_ADMIN())) {
            $operator = $this->userRepository->get($operatorId);
            $companiesByOperator = $this->companyByUserRepository->getCompaniesByUser($operatorId);
        } else {
            $this->logger->critical(
                'User is neither owner nor admin',
                [
                    'user_type' => $userType->getValue(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User is neither owner nor admin: ' . $userType->getValue(),
                Response::HTTP_BAD_REQUEST
            );
        }

        if (empty($operator)) {
            $this->logger->critical(
                'Operator could not be found',
                [
                    'operator_id' => $operatorId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Operator could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        if (empty($companiesByOperator)) {
            $this->logger->critical(
                'Companies could not be found by operator',
                [
                    'operator_id' => $operatorId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Companies could not be found by operator',
                Response::HTTP_NOT_FOUND
            );
        }

        $companies = [];

        foreach ($companiesByOperator as $company) {
            $company = $this->companyRepository->get(CompanyId::fromString($company['company_id']));

            $companies[] = [
                'companyId' => $company->companyId()->toString(),
                'name' => $company->name(),
                'tin' => $company->tin(),
                'email' => $company->email(),
                'address' => $company->address(),
                'traRegistration' => $company->traRegistration(),
                'status' => $company->companyStatus(),
                'createdAt' => $company->createdAt()->format('Y-m-d H:i:s'),
            ];
        }

        return [
            'id' => $operator->userId()->toString(),
            'firstName' => $operator->firstName(),
            'lastName' => $operator->lastName(),
            'email' => $operator->email(),
            'mobileNumber' => $operator->mobileNumber(),
            'companies' => $companies,
            'status' => $operator->status()->getValue(),
            'createdAt' => $operator->createdAt()->format('Y-m-d H:i:s'),
        ];
    }
}
