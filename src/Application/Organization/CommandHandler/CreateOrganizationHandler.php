<?php

declare(strict_types=1);

namespace App\Application\Organization\CommandHandler;

use App\Domain\Model\Organization\Organization;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\Organization\OrganizationStatus;
use App\Domain\Repository\OrganizationRepository;
use App\Domain\Services\SendCredentialsRequest;
use App\Domain\Services\SendCredentialsService;
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
    public const NEW_PASSWORD = 'tanzaniaSimplify123*';

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var OrganizationRepository */
    private OrganizationRepository $organizationRepository;

    /** @var SendCredentialsService */
    private SendCredentialsService $sendCredentials;

    /**
     * @param LoggerInterface $logger
     * @param OrganizationRepository $organizationRepository
     * @param SendCredentialsService $sendCredentials
     */
    public function __construct(
        LoggerInterface $logger,
        OrganizationRepository $organizationRepository,
        SendCredentialsService $sendCredentials
    ) {
        $this->logger = $logger;
        $this->organizationRepository = $organizationRepository;
        $this->sendCredentials = $sendCredentials;
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
            $command->getOwnerPhoneNumber() ?? null,
            OrganizationStatus::ACTIVE(),
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

                $password = base64_encode($this::NEW_PASSWORD);

                $request = new SendCredentialsRequest(
                    'NEW_CREDENTIALS',
                    $organization->getOwnerName(),
                    $password,
                    $organization->getOwnerEmail(),
                    $company->name()
                );

                $response = $this->sendCredentials->onSendCredentials($request);



                return $organizationId->toString();
            }
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

        return '';
    }
}
