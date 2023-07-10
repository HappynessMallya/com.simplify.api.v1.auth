<?php

declare(strict_types=1);

namespace App\Application\Company\V1\CommandHandler;

use App\Application\Company\V1\Command\UpdateCompanyCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\OrganizationRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UpdateCompanyHandler
 * @package App\Application\Company\V1\CommandHandler
 */
class UpdateCompanyHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var OrganizationRepository */
    private OrganizationRepository $organizationRepository;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
     * @param LoggerInterface $logger
     * @param OrganizationRepository $organizationRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        LoggerInterface $logger,
        OrganizationRepository $organizationRepository,
        CompanyRepository $companyRepository
    ) {
        $this->logger = $logger;
        $this->organizationRepository = $organizationRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param UpdateCompanyCommand $command
     * @return bool
     * @throws Exception
     */
    public function handle(UpdateCompanyCommand $command): bool
    {
        $companyId = CompanyId::fromString($command->getCompanyId());
        $company = $this->companyRepository->get($companyId);

        if (empty($company)) {
            $this->logger->critical(
                'Company could not be found',
                [
                    'company_id' => $companyId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        $criteria['updatedAt'] = new DateTime('now');

        if (!empty($command->getName())) {
            $criteria['name'] = $command->getName();
        }

        if (!empty($command->getEmail())) {
            $criteria['email'] = $command->getEmail();
        }

        if (!empty($command->getPhone())) {
            $criteria['phone'] = $command->getPhone();
        }

        if (!empty($command->getAddress())) {
            $criteria['address'] = $command->getAddress();
        }

        if (count($criteria) === 1) {
            $this->logger->critical(
                'You need at least one field to update company',
                [
                    'company_id' => $command->getCompanyId(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'You need at least one field to update company',
                Response::HTTP_BAD_REQUEST
            );
        }

        $isPreRegistered = $this->companyRepository->findOneBy(
            [
                'name' => $command->getName(),
            ]
        );

        if (!empty($isPreRegistered)) {
            $this->logger->critical(
                'Company has pre-registered with the name provided',
                [
                    'name' => $command->getName(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company has pre-registered with the name provided',
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!empty($command->getOrganizationId())) {
            $organizationId = OrganizationId::fromString($command->getOrganizationId());
            $organization = $this->organizationRepository->get($organizationId);

            if (empty($organization)) {
                $this->logger->critical(
                    'Organization could not be found',
                    [
                        'organization_id' => $organizationId->toString(),
                        'method' => __METHOD__,
                    ]
                );

                throw new Exception(
                    'Organization could not be found',
                    Response::HTTP_NOT_FOUND
                );
            }

            $company->setOrganizationId($organizationId);
        }

        try {
            $company->update($criteria);
            $isUpdated = $this->companyRepository->save($company);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Company could not be updated',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company could not be updated. ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($isUpdated) {
            $this->logger->debug(
                'Company updated successfully',
                [
                    'company_id' => $companyId->toString(),
                    'name' => $company->name(),
                    'email' => $company->email(),
                    'phone' => $company->phone(),
                    'address' => $company->address(),
                ]
            );

            return true;
        }

        return false;
    }
}
