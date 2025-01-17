<?php

declare(strict_types=1);

namespace App\Application\Organization\V1\QueryHandler;

use App\Application\Organization\V1\Query\GetOrganizationsByParamsQuery;
use App\Domain\Model\Organization\Organization;
use App\Domain\Model\Organization\OrganizationStatus;
use App\Domain\Repository\OrganizationRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetOrganizationsByParamsHandler
 * @package App\Application\Organization\V1\QueryHandler
 */
class GetOrganizationsByParamsHandler
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
     * @param GetOrganizationsByParamsQuery $query
     * @return array
     * @throws Exception
     */
    public function __invoke(GetOrganizationsByParamsQuery $query): array
    {
        $criteria = [];

        if (!empty($query->getName())) {
            $criteria['name'] = trim($query->getName());
        }

        if (!empty($query->getOwnerName())) {
            $criteria['ownerName'] = trim($query->getOwnerName());
        }

        if (!empty($query->getOwnerEmail())) {
            $criteria['ownerEmail'] = trim($query->getOwnerEmail());
        }

        if (!empty($query->getOwnerPhoneNumber())) {
            $criteria['ownerPhoneNumber'] = trim($query->getOwnerPhoneNumber());
        }

        if ($query->getStatus() !== 'ALL') {
            $criteria['status'] = OrganizationStatus::byValue(trim($query->getStatus()))->getValue();
        }

        $organizationsByCriteria = $this->organizationRepository->findByCriteria($criteria);

        if (empty($organizationsByCriteria)) {
            $this->logger->critical(
                'Organizations could not be found by the search criteria',
                [
                    'criteria' => $criteria,
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Organizations could not be found by the search criteria',
                Response::HTTP_NOT_FOUND
            );
        }

        $organizations = [];

        /** @var Organization $organization */
        foreach ($organizationsByCriteria as $organization) {
            $companiesByOrganization = $this->organizationRepository->getCompaniesByOrganization(
                $organization->getOrganizationId()
            );

            $organizations[] = [
                'organizationId' => $organization->getOrganizationId()->toString(),
                'name' => $organization->getName(),
                'ownerName' => $organization->getOwnerName(),
                'ownerEmail' => $organization->getOwnerEmail(),
                'ownerPhoneNumber' => $organization->getOwnerPhoneNumber() ?? '',
                'status' => $organization->getStatus()->getValue(),
                'companies' => count($companiesByOrganization),
                'createdAt' => $organization->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return $organizations;
    }
}
