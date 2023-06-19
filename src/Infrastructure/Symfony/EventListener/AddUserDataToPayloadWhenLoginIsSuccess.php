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
    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /**
     * AddUserDataToPayloadWhenLoginIsSuccess constructor
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
        $data = $event->getData();
        $user = $event->getUser();

        if ($user instanceof UserEntity || $user instanceof User) {
            $this->userRepository->login($user->userId());
        } else {
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

        if ($user->getStatus()->sameValueAs(UserStatus::CHANGE_PASSWORD())) {
            $data['data']['change_password'] = 1;
        }

        $event->setData($data);
    }
}
