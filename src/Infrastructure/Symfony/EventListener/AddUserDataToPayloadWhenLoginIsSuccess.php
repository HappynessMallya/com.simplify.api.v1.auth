<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Domain\Model\User\User;
use App\Domain\Repository\CompanyRepository;
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
     * @var CompanyRepository
     */
    private $companyRepository;

    public function __construct(UserRepository $userRepository, CompanyRepository $companyRepository)
    {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
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
        }

        $user = $this->userRepository->findOneBy(['email' => $user->getUsername()]);
        $company = $this->companyRepository->get($user->companyId());

        $data['data'] = [
            'roles' => $user->roles(),
            'adm' => $user->isAdmin(),
            'username' => $user->getUsername(),
            'company' => [
                'id' => $company->companyId()->toString(),
                'name' => $company->name(),
            ],
        ];

        $event->setData($data);
    }
}
