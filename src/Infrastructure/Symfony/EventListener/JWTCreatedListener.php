<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Domain\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
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
     * JWTCreatedListener constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param JWTCreatedEvent $event
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();
        $payload = $event->getData();

        if (!$user instanceof UserInterface) {
            return;
        }

        $user = $this->userRepository->findOneBy(['email' => $user->getUsername()]);
        $payload['companyId'] = $user->getCompanyId();

        $event->setData($payload);
    }
}
