<?php

declare(strict_types=1);

namespace App\Application\User\QueryHandler;

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
 * @package App\Application\User\V2\QueryHandler
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

        if ($userType->sameValueAs(UserType::TYPE_OWNER()) || $userType->sameValueAs(UserType::TYPE_ADMIN())) {
            $operatorsByOrganization = $this->companyByUserRepository->getOperatorsByOrganization($organizationId);
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

        if (empty($operatorsByOrganization)) {
            $this->logger->critical(
                'Operators not found by organization ID',
                [
                    'user_id' => $organizationId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Operators not found by organization ID: ' . $organizationId->toString(),
                Response::HTTP_NOT_FOUND
            );
        }

        $operators = [];

        foreach ($operatorsByOrganization as $operator) {
            $operatorFound = $this->userRepository->get(UserId::fromString($operator['user_id']));
            $companiesByOperator = $this->companyByUserRepository->getCompaniesByUser($operatorFound->userId());

            $operatorsCompanies = [];

            foreach ($companiesByOperator as $company) {
                $companyId = CompanyId::fromString($company['company_id']);
                $companyFound = $this->companyRepository->get($companyId);

                $operatorsCompanies[] = [
                    'companyId' => $companyId->toString(),
                    'companyName' => $companyFound->name(),
                    'status' => $companyFound->companyStatus(),
                ];
            }

            $operators[] = [
                'userId' => $operatorFound->userId()->toString(),
                'firstName' => $operatorFound->firstName(),
                'lastName' => $operatorFound->lastName(),
                'email' => $operatorFound->email(),
                'mobileNumber' => $operatorFound->mobileNumber(),
                'companies' => $operatorsCompanies,
                'status' => $operatorFound->status()->getValue(),
                'createdAt' => $operatorFound->createdAt()->format(DATE_ATOM),
            ];
        }

        return $operators;
    }
}
