<?php

declare(strict_types=1);

namespace App\Application\User\V2\QueryHandler;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetCompaniesByParamsHandler
 * @package App\Application\User\V2\QueryHandler
 */
class GetCompaniesByParamsHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var CompanyByUserRepository */
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
     * @param GetCompaniesByParamsQuery $query
     * @return array|null
     * @throws Exception
     */
    public function __invoke(GetCompaniesByParamsQuery $query): array
    {
        $userId = UserId::fromString($query->getUserId());
        $userType = UserType::byName($query->getUserType());

        if ($userType->sameValueAs(UserType::TYPE_OWNER())) {
            $companiesByUser = $this->companyByUserRepository->getCompaniesByUser($userId);
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
            $criteria = [
                'companyId' => CompanyId::fromString($company['company_id']),
            ];

            if (!empty($query->getCompanyName())) {
                $criteria['name'] = trim($query->getCompanyName());
            }

            if (!empty($query->getTin())) {
                $criteria['tin'] = trim($query->getTin());
            }

            if (!empty($query->getEmail())) {
                $criteria['email'] = trim($query->getEmail());
            }

            if (!empty($query->getMobileNumber())) {
                $criteria['phone'] = trim($query->getMobileNumber());
            }

            if (!empty($query->getSerial())) {
                $criteria['serial'] = trim($query->getSerial());
            }

            if ($query->getStatus() !== 'ALL') {
                $criteria['companyStatus'] = CompanyStatus::byValue(trim($query->getStatus()))->getValue();
            }

            $company = $this->companyRepository->findOneBy($criteria);

            if (!empty($company)) {
                if (($company->traRegistration()['VRN'] == $query->getVrn()) || empty($query->getVrn())) {
                    $companies[] = [
                        'companyId' => $company->companyId()->toString(),
                        'name' => $company->name(),
                        'tin' => $company->tin(),
                        'email' => $company->email(),
                        'phone' => $company->phone(),
                        'address' => $company->address(),
                        'traRegistration' => $company->traRegistration(),
                        'status' => $company->companyStatus(),
                        'createdAt' => $company->createdAt()->format(DATE_ATOM),
                    ];
                }
            }
        }

        return $companies;
    }
}
