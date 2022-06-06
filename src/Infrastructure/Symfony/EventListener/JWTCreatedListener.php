<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Domain\Model\User\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Services\CompanyStatusOnTraRequest;
use App\Domain\Services\TraIntegrationService;
use App\Infrastructure\Symfony\Security\UserEntity;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class JWTCreatedListener
 * @package App\Infrastructure\Symfony\EventListener
 */
class JWTCreatedListener
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var TraIntegrationService
     */
    private TraIntegrationService $traIntegrationService;

    /**
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param TraIntegrationService $traIntegrationService
     * @param LoggerInterface $logger
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        TraIntegrationService $traIntegrationService,
        LoggerInterface $logger
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->traIntegrationService = $traIntegrationService;
        $this->logger = $logger;
    }

    /**
     * @param JWTCreatedEvent $event
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();

        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$user instanceof UserEntity && !$user instanceof User) {
            $jwtUser = $user;
            $user = $this->userRepository->findOneBy(['username' => $jwtUser->getUsername()]);

            if (empty($user)) {
                $user = $this->userRepository->findOneBy(['email' => $jwtUser->getUsername()]);
            }
        }

        $payload['username'] = $user->getEmail();
        $payload['companyId'] = $user->getCompanyId();

        $company = $this->companyRepository->get($user->companyId());

        $companyStatusOnTraRequest = new CompanyStatusOnTraRequest(
            $company->companyId()->toString(),
            (string) $company->tin()
        );

        $companyStatusOnTraResponse = $this->traIntegrationService->requestCompanyStatusOnTra(
            $companyStatusOnTraRequest
        );

        if (!$companyStatusOnTraResponse->isSuccess()) {
            $this->logger->critical(
                'An error occurred when request status of company on TRA',
                [
                    'company_id' => $companyStatusOnTraRequest->getCompanyId(),
                    'tin' => $companyStatusOnTraRequest->getTin(),
                    'error_message' => $companyStatusOnTraResponse->getErrorMessage(),
                ]
            );
        }

        $company = $this->companyRepository->get($user->companyId());

        if (!empty($company) && !empty($company->traRegistration())) {
            $payload['companyName'] = $company->name();
            $payload['vrn'] = $company->traRegistration()['VRN'] !== 'NOT REGISTERED';
        }

        $event->setData($payload);
    }
}
