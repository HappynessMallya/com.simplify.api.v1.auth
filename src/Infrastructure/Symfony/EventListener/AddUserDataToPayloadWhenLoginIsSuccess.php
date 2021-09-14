<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Domain\Model\User\User;
use App\Domain\Model\User\UserStatus;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Symfony\Security\UserEntity;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

/**
 * Class AddUserDataToPayloadWhenLoginIsSuccess
 * @package App\Infrastructure\Symfony\EventListener
 */
class AddUserDataToPayloadWhenLoginIsSuccess
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * AddUserDataToPayloadWhenLoginIsSuccess constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
            $user = $this->userRepository->findOneBy(['email' => $user->getUsername()]);
        }

        if ($user->getStatus()->sameValueAs(UserStatus::CHANGE_PASSWORD())) {
            $data['data']['change_password'] = 1;
        }

        $event->setData($data);
    }
}
