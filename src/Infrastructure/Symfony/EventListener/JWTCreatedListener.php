<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Application\Company\Command\RequestAuthenticationTraCommand;
use App\Domain\Model\User\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Services\CompanyStatusOnTraRequest;
use App\Domain\Services\TraIntegrationService;
use App\Infrastructure\Symfony\Security\UserEntity;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
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
    private UserRepository $userRepository;

    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $messageBus;

    /**
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param LoggerInterface $logger
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        LoggerInterface $logger,
        MessageBusInterface $messageBus
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->logger = $logger;
        $this->messageBus = $messageBus;
    }

    /**
     * @param JWTCreatedEvent $event
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $this->logger->debug(
            'JWT created successfully',
            [
                'time' => microtime()
            ]
        );

        $user = $event->getUser();
        $payload = $event->getData();

        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$user instanceof UserEntity && !$user instanceof User) {
            $jwtUser = $user;
            $user = $this->userRepository->findOneBy(['email' => $jwtUser->getUsername()]);
        }

        $this->logger->debug(
            'Authentication Successfully',
            [
                'user_id' => $user->getUserId(),
                'username' => $user->getUsername(),
                'time' => microtime(),
            ]
        );

        $payload['username'] = $user->getEmail();
        $payload['companyId'] = $user->getCompanyId();

        $company = $this->companyRepository->get($user->companyId());

        if (!empty($company->traRegistration())) {
            $command = new RequestAuthenticationTraCommand(
                $company->companyId()->toString(),
                (string) $company->tin(),
                $company->traRegistration()['USERNAME'],
                $company->traRegistration()['PASSWORD']
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (Exception $exception) {
                $this->logger->critical(
                    'An error has been occurred when trying request authentication in TRA',
                    [
                        'companyId' => $company->companyId()->toString(),
                        'tin' => $company->tin(),
                        'error' => $exception->getMessage(),
                        'code' => $exception->getCode(),
                        'method' => __METHOD__,
                    ]
                );
            }
        }

        if (!empty($company) && !empty($company->traRegistration())) {
            $payload['companyName'] = $company->name();
            $payload['vrn'] = $company->traRegistration()['VRN'] !== 'NOT REGISTERED';
        }

        $event->setData($payload);
    }
}
