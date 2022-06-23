<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Domain\Model\User\User;
use App\Domain\Model\User\UserStatus;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Symfony\Security\UserEntity;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Psr\Log\LoggerInterface;

/**
 * Class AddUserDataToPayloadWhenLoginIsSuccess
 * @package App\Infrastructure\Symfony\EventListener
 */
class AddUserDataToPayloadWhenLoginIsSuccess
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    private LoggerInterface $logger;

    /**
     * AddUserDataToPayloadWhenLoginIsSuccess constructor.
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     */
    public function __construct(UserRepository $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $this->logger->debug(
            'Authentication Successfully',
            [
                'time' => microtime(true),

            ]
        );
        $data = $event->getData();
        $user = $event->getUser();

        if ($user instanceof UserEntity || $user instanceof User) {
            $this->userRepository->login($user->userId());
        } else {
            $user = $this->userRepository->findOneBy(['email' => $user->getUsername()]);
        }

        $this->logger->debug(
            'User found successfully',
            [
                'user_id' => $user->getUserId(),
                'username' => $user->getUsername(),
                'time' => microtime(true),
            ]
        );

        if ($user->getStatus()->sameValueAs(UserStatus::CHANGE_PASSWORD())) {
            $data['data']['change_password'] = 1;
        }

        $event->setData($data);
    }
}
