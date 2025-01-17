<?php

declare(strict_types=1);

namespace App\Application\Organization\V1\CommandHandler;

use App\Application\Organization\V1\Command\ChangeOrganizationStatusCommand;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\Organization\OrganizationStatus;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\OrganizationRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChangeOrganizationStatusHandler
 * @package App\Application\Organization\V1\CommandHandler
 */
class ChangeOrganizationStatusHandler
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
     * @param ChangeOrganizationStatusCommand $command
     * @return bool
     * @throws Exception
     */
    public function __invoke(ChangeOrganizationStatusCommand $command): bool
    {
        $organizationId = OrganizationId::fromString($command->getOrganizationId());
        $userTypeWhoChangeStatus = UserType::byName($command->getUserType());
        $newStatus = OrganizationStatus::byName($command->getStatus());

        if (
            $userTypeWhoChangeStatus->sameValueAs(UserType::TYPE_OWNER()) ||
            $userTypeWhoChangeStatus->sameValueAs(UserType::TYPE_ADMIN())
        ) {
            $organization = $this->organizationRepository->get($organizationId);
        } else {
            $this->logger->critical(
                'User who is making the change is neither owner nor admin',
                [
                    'user_type' => $userTypeWhoChangeStatus->getValue(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User who is making the change is neither owner nor admin: ' . $userTypeWhoChangeStatus->getValue(),
                Response::HTTP_BAD_REQUEST
            );
        }

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

        if ($organization->getStatus()->is($newStatus)) {
            $this->logger->critical(
                'Organization status is the same one',
                [
                    'organization_status' => $organization->getStatus()->toString(),
                    'new_status' => $newStatus->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Organization status is the same one: ' . $organization->getStatus()->toString(),
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $organization->update(
                [
                    'status' => $newStatus->toString(),
                    'updatedAt' => new DateTime('now'),
                ]
            );

            $isStatusChanged = $this->organizationRepository->save($organization);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Organization status could not be updated',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Organization status could not be updated. ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($isStatusChanged) {
            $this->logger->debug(
                'Organization status updated successfully',
                [
                    'organization_id' => $organization->getOrganizationId(),
                    'name' => $organization->getName(),
                    'owner_name' => $organization->getOwnerName(),
                    'owner_email' => $organization->getOwnerEmail(),
                ]
            );

            return true;
        }

        return false;
    }
}
