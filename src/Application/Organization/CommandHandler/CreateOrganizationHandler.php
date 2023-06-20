<?php

declare(strict_types=1);

namespace App\Application\Organization\CommandHandler;

use App\Domain\Model\Organization\Organization;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\Organization\OrganizationStatus;
use App\Domain\Repository\OrganizationRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreateOrganizationHandler
 * @package App\Application\Organization\CommandHandler
 */
class CreateOrganizationHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var OrganizationRepository */
    private OrganizationRepository $organizationRepository;

    /**
     * @param LoggerInterface $logger
     * @param OrganizationRepository $organizationRepository
     */
    public function __construct(
        LoggerInterface $logger,
        OrganizationRepository $organizationRepository
    ) {
        $this->logger = $logger;
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @param CreateOrganizationCommand $command
     * @return string|null
     * @throws Exception
     */
    public function handle(CreateOrganizationCommand $command): string
    {
        $organizationId = OrganizationId::generate();

        $organization = Organization::create(
            $organizationId,
            $command->getName(),
            $command->getOwnerName(),
            $command->getOwnerEmail(),
            $command->getOwnerPhoneNumber() ?? '',
            OrganizationStatus::STATUS_ACTIVE(),
            new DateTime('now'),
            null,
        );

        $organizationRegistered = $this->organizationRepository->findOneBy(
            [
                'name' => $command->getName(),
            ]
        );

        if (!empty($organizationRegistered)) {
            $this->logger->critical(
                'Organization previously registered with the provided name',
                [
                    'name' => $command->getName(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Organization previously registered with the provided name',
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $isSaved = $this->organizationRepository->save($organization);

            if ($isSaved) {
                $this->logger->debug(
                    'Organization registered successfully',
                    [
                        'organization_id' => $organization->getOrganizationId(),
                        'name' => $organization->getName(),
                        'owner_name' => $organization->getOwnerName(),
                        'owner_email' => $organization->getOwnerEmail(),
                        'method' => __METHOD__,
                    ]
                );
            }

            return $organizationId->toString();
        } catch (Exception $exception) {
            $this->logger->critical(
                'Organization could not be registered',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
