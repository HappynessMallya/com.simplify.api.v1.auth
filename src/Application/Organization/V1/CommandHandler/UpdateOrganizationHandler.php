<?php

declare(strict_types=1);

namespace App\Application\Organization\V1\CommandHandler;

use App\Application\Organization\V1\Command\UpdateOrganizationCommand;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Repository\OrganizationRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UpdateOrganizationHandler
 * @package App\Application\Organization\V1\CommandHandler
 */
class UpdateOrganizationHandler
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
     * @param UpdateOrganizationCommand $command
     * @return bool|null
     * @throws Exception
     */
    public function handle(UpdateOrganizationCommand $command): ?bool
    {
        $organizationId = OrganizationId::fromString($command->getOrganizationId());
        $organization = $this->organizationRepository->get($organizationId);

        if (empty($organization)) {
            $this->logger->critical(
                'Organization could not be found',
                [
                    'organization_id' => $command->getOrganizationId(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Organization could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        $criteria['updatedAt'] = new DateTime('now');

        if (!empty($command->getName())) {
            $criteria['name'] = $command->getName();
        }

        if (!empty($command->getOwnerName())) {
            $criteria['ownerName'] = $command->getOwnerName();
        }

        if (!empty($command->getOwnerEmail())) {
            $criteria['ownerEmail'] = $command->getOwnerEmail();
        }

        if (!empty($command->getOwnerPhoneNumber())) {
            $criteria['ownerPhoneNumber'] = $command->getOwnerPhoneNumber();
        }

        if (count($criteria) === 1) {
            $this->logger->critical(
                'You need at least one data to update the organization',
                [
                    'organization_id' => $command->getOrganizationId(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'You need at least one data to update the organization',
                Response::HTTP_BAD_REQUEST
            );
        }

        $organization->update($criteria);

        try {
            $response = $this->organizationRepository->save($organization);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Organization could not be updated',
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

        return $response;
    }
}
