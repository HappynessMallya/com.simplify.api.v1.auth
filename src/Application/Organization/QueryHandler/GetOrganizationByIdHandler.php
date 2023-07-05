<?php

declare(strict_types=1);

namespace App\Application\Organization\QueryHandler;

use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Repository\OrganizationRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetOrganizationByIdHandler
 * @package App\Application\Organization\QueryHandler
 */
class GetOrganizationByIdHandler
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
     * @param GetOrganizationByIdQuery $query
     * @return array
     * @throws Exception
     */
    public function __invoke(GetOrganizationByIdQuery $query): array
    {
        $organizationId = OrganizationId::fromString($query->getOrganizationId());
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

        return [
            'id' => $organization->getOrganizationId()->toString(),
            'name' => $organization->getName(),
            'ownerName' => $organization->getOwnerName(),
            'ownerEmail' => $organization->getOwnerEmail(),
            'ownerPhoneNumber' => $organization->getOwnerPhoneNumber(),
            'status' => $organization->getStatus()->getValue(),
            'createdAt' => $organization->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
