<?php

declare(strict_types=1);

namespace App\Application\User\V2\QueryHandler;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetCompaniesByUserTypeHandler
 * @package App\Application\User\V2\QueryHandler
 */
class GetCompaniesByUserTypeHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var CompanyRepository  */
    private CompanyRepository $companyRepository;

    /** @var CompanyByUserRepository  */
    private CompanyByUserRepository $companyByUserRepository;

    /**
     * @param LoggerInterface $logger
     * @param CompanyRepository $companyRepository
     * @param CompanyByUserRepository $companyByUserRepository
     */
    public function __construct(
        LoggerInterface $logger,
        CompanyRepository $companyRepository,
        CompanyByUserRepository $companyByUserRepository
    ) {
        $this->logger = $logger;
        $this->companyRepository = $companyRepository;
        $this->companyByUserRepository = $companyByUserRepository;
    }

    /**
     * @param GetCompaniesByUserTypeQuery $query
     * @return array
     * @throws Exception
     */
    public function __invoke(GetCompaniesByUserTypeQuery $query): array
    {
        $userId = UserId::fromString($query->getUserId());
        $userType = UserType::byName($query->getUserType());

        if ($userType->sameValueAs(UserType::TYPE_OWNER())) {
            $companiesByUser = $this->companyByUserRepository->getCompaniesByUser($userId);
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

        if (empty($companiesByUser)) {
            $this->logger->critical(
                'Companies not found by user',
                [
                    'user_id' => $userId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Companies not found by user: ' . $userId->toString(),
                Response::HTTP_NOT_FOUND
            );
        }

        $companies = [];

        foreach ($companiesByUser as $company) {
            $company = $this->companyRepository->get(CompanyId::fromString($company['company_id']));

            $companies[] = [
                'companyId' => $company->companyId()->toString(),
                'name' => $company->name(),
                'tin' => $company->tin(),
                'email' => $company->email(),
                'address' => $company->address(),
                'traRegistration' => $company->traRegistration(),
                'createdAt' => $company->createdAt()->format(DATE_ATOM),
                'status' => $company->companyStatus(),
            ];
        }

        return $companies;
    }
}
