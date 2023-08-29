<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Domain\Model\User\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Symfony\Security\UserEntity;
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
    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var MessageBusInterface */
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

            $criteria = [
                'email' => $jwtUser->getUsername(),
            ];

            $user = $this->userRepository->findOneBy($criteria);

            if (empty($user)) {
                $criteria = [
                    'username' => $jwtUser->getUsername(),
                ];

                $user = $this->userRepository->findOneBy($criteria);
            }
        }

        $payload['username'] = $user->getEmail();
        $payload['email'] = $user->getEmail();
        $payload['companyId'] = $user->getCompanyId();

        $event->setData($payload);
    }
}
