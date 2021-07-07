<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Domain\Model\User\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Symfony\Security\UserEntity;
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
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * JWTCreatedListener constructor.
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(UserRepository $userRepository, CompanyRepository $companyRepository)
    {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
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

        if (!empty($company) && !empty($company->traRegistration())) {
            $payload['companyName'] = $company->name();
            $payload['vrn'] = $company->traRegistration()['VRN'] !== 'NOT REGISTERED';
        }

        $event->setData($payload);
    }
}
