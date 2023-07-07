<?php

declare(strict_types=1);

namespace App\Application\Organization\V1\CommandHandler;

use App\Application\Organization\V1\Command\CreateOrganizationCommand;
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
 * @package App\Application\Organization\V1\CommandHandler
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
     * @return string
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
            $command->getOwnerPhoneNumber() ?? null,
            OrganizationStatus::ACTIVE(),
            new DateTime('now'),
            null,
        );

        $isPreRegistered = $this->organizationRepository->findOneBy(
            [
                'name' => $command->getName(),
            ]
        );

        if (!empty($isPreRegistered)) {
            $this->logger->critical(
                'Organization has pre-registered with the name provided',
                [
                    'name' => $command->getName(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Organization has pre-registered with the name provided',
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $isSaved = $this->organizationRepository->save($organization);
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
                'Organization could not be registered' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($isSaved) {
            $this->logger->debug(
                'Organization registered successfully',
                [
                    'organization_id' => $organization->getOrganizationId(),
                    'name' => $organization->getName(),
                    'owner_name' => $organization->getOwnerName(),
                    'owner_email' => $organization->getOwnerEmail(),
                ]
            );

            return $organizationId->toString();
        }

        return '';
    }
}
