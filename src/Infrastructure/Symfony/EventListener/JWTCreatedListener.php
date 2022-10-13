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
        $startTimeJwtListener = microtime(true);

        $user = $event->getUser();
        $payload = $event->getData();

        if (!$user instanceof UserInterface) {
            return;
        }

        $start = microtime(true);
        if (!$user instanceof UserEntity && !$user instanceof User) {
            $jwtUser = $user;
            $user = $this->userRepository->findOneBy(['email' => $jwtUser->getUsername()]);
        }
        $end = microtime(true);

        $this->logger->debug(
            'Time duration of find user for JWT process',
            [
                'time' => $end - $start,
                'user_id' => $user->getUserId(),
            ]
        );

        $payload['username'] = $user->getEmail();
        $payload['companyId'] = $user->getCompanyId();

        $start = microtime(true);
        $company = $this->companyRepository->get($user->companyId());
        $end = microtime(true);

        $this->logger->debug(
            'Time duration of get company data',
            [
                'time' =>  $end - $start,
                'company_id' => $company->companyId()->toString(),
                'method' => __METHOD__,
            ]
        );

        if (!empty($company->traRegistration())) {
            $command = new RequestAuthenticationTraCommand(
                $company->companyId()->toString(),
                (string) $company->tin(),
                $company->traRegistration()['USERNAME'],
                $company->traRegistration()['PASSWORD']
            );

            $start = microtime(true);
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
            $end = microtime(true);

            $this->logger->debug(
                'Time duration of execution of async command',
                [
                    'time' => $end - $start,
                    'user_id' => $user->userId()->toString(),
                    'method' => __METHOD__,
                ]
            );
        }

        if (!empty($company) && !empty($company->traRegistration())) {
            $payload['companyName'] = $company->name();
            $payload['vrn'] = $company->traRegistration()['VRN'] !== 'NOT REGISTERED';
        }

        $event->setData($payload);

        $endTimeJwtListener = microtime(true);
        $this->logger->debug(
            'Time duration of JWT Created Listener',
            [
                'time' => $endTimeJwtListener - $startTimeJwtListener,
                'method' => __METHOD__,
            ]
        );
    }
}
