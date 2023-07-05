<?php

declare(strict_types=1);

namespace App\Application\Organization\QueryHandler;

use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetCompaniesByParamsHandler
 * @package App\Application\Organization\QueryHandler
 */
class GetCompaniesByParamsHandler
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
     * @param GetCompaniesByParamsQuery $query
     * @return array|null
     * @throws Exception
     */
    public function __invoke(GetCompaniesByParamsQuery $query): array
    {
        $organizationId = OrganizationId::fromString($query->getOrganizationId());
        $userId = UserId::fromString($query->getUserId());
        $userType = UserType::byName($query->getUserType());

        if ($userType->sameValueAs(UserType::TYPE_OWNER())) {
            $criteria = [];
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

            $companies = $this->companyRepository->getByOrganizationIdAndParams($organizationId, $criteria);
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
                'Companies not found by the search criteria',
                [
                    'user_id' => $userId->toString(),
                    'organization_id' => $organizationId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Companies not found by the search criteria',
                Response::HTTP_NOT_FOUND
            );
        }

        $responseCompanies = [];

        foreach ($companies as $company) {
            if (($company->traRegistration()['VRN'] == $query->getVrn()) || empty($query->getVrn())) {
                $responseCompanies[] = [
                    'companyId' => $company->companyId()->toString(),
                    'name' => $company->name(),
                    'tin' => $company->tin(),
                    'email' => $company->email(),
                    'phone' => $company->phone(),
                    'address' => $company->address(),
                    'traRegistration' => $company->traRegistration(),
                    'status' => $company->companyStatus(),
                    'createdAt' => $company->createdAt()->format('Y-m-d H:i:s'),
                ];
            }
        }

        return $responseCompanies;
    }
}
