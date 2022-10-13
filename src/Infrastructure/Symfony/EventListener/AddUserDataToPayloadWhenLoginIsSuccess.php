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
    private $userRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

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
        $startTimeAddUserPayload = microtime(true);

        $data = $event->getData();
        $user = $event->getUser();

        $start = microtime(true);
        if ($user instanceof UserEntity || $user instanceof User) {
            $this->userRepository->login($user->userId());
        } else {
            $user = $this->userRepository->findOneBy(['email' => $user->getUsername()]);
        }
        $end = microtime(true);
        $this->logger->debug(
            'Time duration of login user process',
            [
                'time' => $end - $start,
                'user' => $user->userId()->toString(),
                'company' => $user->companyId()->toString(),
                'method' => __METHOD__
            ]
        );

        if ($user->getStatus()->sameValueAs(UserStatus::CHANGE_PASSWORD())) {
            $data['data']['change_password'] = 1;
        }

        $event->setData($data);

        $endTimeAddUserPayload = microtime(true);
        $this->logger->debug(
            'Time duration of Add User data to Payload when login is success',
            [
                'time' => $endTimeAddUserPayload - $startTimeAddUserPayload,
                'method' => __METHOD__,
            ]
        );
    }
}
