<?php

declare(strict_types=1);

namespace App\Application\Company\V2\CommandHandler;

use App\Application\Company\V2\Command\RegisterCompanyCommand;
use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\OrganizationRepository;
use App\Domain\Services\CreateSubscriptionRequest;
use App\Domain\Services\SubscriptionService;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RegisterCompanyHandler
 * @package App\Application\Company\V2\CommandHandler
 */
class RegisterCompanyHandler
{
    public const SUBSCRIPTION_TYPE = 'ESTABLISHED';

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var OrganizationRepository */
    private OrganizationRepository $organizationRepository;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var SubscriptionService  */
    private SubscriptionService $subscriptionService;

    /**
     * @param LoggerInterface $logger
     * @param OrganizationRepository $organizationRepository
     * @param CompanyRepository $companyRepository
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(
        LoggerInterface $logger,
        OrganizationRepository $organizationRepository,
        CompanyRepository $companyRepository,
        SubscriptionService $subscriptionService
    ) {
        $this->logger = $logger;
        $this->organizationRepository = $organizationRepository;
        $this->companyRepository = $companyRepository;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @param RegisterCompanyCommand $command
     * @return string|null
     * @throws Exception
     */
    public function handle(RegisterCompanyCommand $command): string
    {
        $organizationId = OrganizationId::fromString($command->getOrganizationId());
        $organization = $this->organizationRepository->get($organizationId);

        if (empty($organization)) {
            $this->logger->critical(
                'Organization could not be found',
                [
                    'organization_id' => $organizationId,
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Organization could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        $companyRegistered = $this->companyRepository->findOneBy(
            [
                'serial' => $command->getSerial(),
            ]
        );

        if (!empty($companyRegistered)) {
            $this->logger->critical(
                'Company has pre-registered with the Serial number provided',
                [
                    'tin' => $command->getTin(),
                    'serial' => $command->getSerial(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company has pre-registered with the Serial number provided',
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
                    'tin' => $command->getTin(),
                    'serial' => $command->getSerial(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company has pre-registered with the name provided',
                Response::HTTP_BAD_REQUEST
            );
        }

        $companyId = CompanyId::generate();

        $company = Company::create(
            $companyId,
            $command->getName(),
            (int) $command->getTin(),
            $command->getAddress(),
            $command->getEmail(),
            $command->getPhone(),
            new DateTime(),
            CompanyStatus::STATUS_ACTIVE(),
            $command->getSerial(),
            $organizationId
        );

        try {
            $isSaved = $this->companyRepository->save($company);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Company could not be registered',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Company could not be registered. ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($isSaved) {
            $this->logger->debug(
                'Company registered successfully',
                [
                    'company_id' => $companyId->toString(),
                    'name' => $company->name(),
                    'tin' => $company->tin(),
                    'serial' => $company->serial(),
                    'email' => $company->email(),
                ]
            );

            return $companyId->toString();
        }

        $subscriptionRequest = new CreateSubscriptionRequest(
            $company->companyId()->toString(),
            $company->createdAt()->format('Y-m-d'),
            self::SUBSCRIPTION_TYPE
        );

        $subscription = $this->subscriptionService->createSubscription($subscriptionRequest);
        if (!$subscription->isSuccess()) {
            $this->logger->critical(
                'The subscription could not be created successfully',
                [
                    'company_id' => $company->companyId()->toString(),
                    'error_message' => $subscription->getErrorMessage(),
                ]
            );
        }

        $this->logger->info(
            'Company with subscription has been created successfully',
            [
                'company_id' => $company->companyId()->toString(),
            ]
        );

        return '';
    }
}
