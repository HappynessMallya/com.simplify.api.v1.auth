<?php

declare(strict_types=1);

namespace App\Application\Organization\V1\QueryHandler;

use App\Application\Organization\V1\Query\GetCompaniesByParamsQuery;
use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetCompaniesByParamsHandler
 * @package App\Application\Organization\V1\QueryHandler
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
     * @return array
     * @throws Exception
     */
    public function __invoke(GetCompaniesByParamsQuery $query): array
    {
        $organizationId = OrganizationId::fromString($query->getOrganizationId());
        $userType = UserType::byName($query->getUserType());

        if (
            $userType->sameValueAs(UserType::TYPE_OWNER()) ||
            $userType->sameValueAs(UserType::TYPE_ADMIN())
        ) {
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

            $companiesByCriteria = $this->companyRepository->getByOrganizationIdAndParams($organizationId, $criteria);
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

        if (empty($companiesByCriteria)) {
            $this->logger->critical(
                'Companies could not be found by the search criteria',
                [
                    'organization_id' => $organizationId->toString(),
                    'criteria' => $criteria,
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Companies could not be found by the search criteria',
                Response::HTTP_NOT_FOUND
            );
        }

        $companies = [];

        /** @var Company $company */
        foreach ($companiesByCriteria as $company) {
            if (empty($query->getVrn()) || ($company->traRegistration()['VRN'] == $query->getVrn())) {
                $companies[] = [
                    'companyId' => $company->companyId()->toString(),
                    'name' => $company->name(),
                    'tin' => $company->tin(),
                    'serial' => $company->serial(),
                    'email' => $company->email(),
                    'phone' => $company->phone() ?? '',
                    'address' => $company->address(),
                    'traRegistration' => $company->traRegistration(),
                    'status' => $company->companyStatus(),
                    'enable' => $company->isEnable(),
                    'createdAt' => $company->createdAt()->format('Y-m-d H:i:s'),
                ];
            }
        }

        return $companies;
    }
}
