<?php

declare(strict_types=1);

namespace App\Application\Organization\QueryHandler;

use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\Organization\OrganizationStatus;
use App\Domain\Repository\OrganizationRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChangeOrganizationStatusByIdHandler
 * @package App\Application\Organization\QueryHandler
 */
class ChangeOrganizationStatusByIdHandler
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
     * @param ChangeOrganizationStatusByIdQuery $query
     * @return bool
     * @throws Exception
     */
    public function __invoke(ChangeOrganizationStatusByIdQuery $query): bool
    {
        $organizationId = OrganizationId::fromString($query->getOrganizationId());
        $newStatus = OrganizationStatus::byName($query->getNewStatus());
        $organization = $this->organizationRepository->get($organizationId);

        if (empty($organization)) {
            $this->logger->critical(
                'Organization not found by ID',
                [
                    'organization_id' => $organizationId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Organization not found by ID: ' . $organizationId->toString(),
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

        $organization->update(
            [
                'status' => $newStatus->toString(),
                'updatedAt' => new DateTime('now'),
            ]
        );

        return $this->organizationRepository->save($organization);
    }
}
