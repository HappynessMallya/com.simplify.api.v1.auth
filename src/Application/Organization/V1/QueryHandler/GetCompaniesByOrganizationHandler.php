<?php

declare(strict_types=1);

namespace App\Application\Organization\V1\QueryHandler;

use App\Application\Organization\V1\Query\GetCompaniesByOrganizationQuery;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetCompaniesByOrganizationHandler
 * @package App\Application\Organization\V1\QueryHandler
 */
class GetCompaniesByOrganizationHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
     * @param LoggerInterface $logger
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        LoggerInterface $logger,
        CompanyRepository $companyRepository
    ) {
        $this->logger = $logger;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param GetCompaniesByOrganizationQuery $query
     * @return array
     * @throws Exception
     */
    public function __invoke(GetCompaniesByOrganizationQuery $query): array
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
                    'user_id' => $query->getUserId(),
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
                'Companies not found by organization',
                [
                    'user_id' => $userId->toString(),
                    'organization_id' => $organizationId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Companies not found by organization',
                Response::HTTP_NOT_FOUND
            );
        }

        $companiesResult = [];
        foreach ($companies as $company) {
            $companiesResult[] = [
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

        return $companiesResult;
    }
}
