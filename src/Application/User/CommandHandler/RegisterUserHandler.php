<?php
declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\RegisterUserCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserRole;
use App\Domain\Model\User\UserStatus;
use App\Domain\Repository\UserRepository;
use App\Domain\Services\User\PasswordEncoder;
use Psr\Log\LoggerInterface;

/**
 * Class RegisterUserHandler
 * @package App\Application\ApiUser\CommandHandler
 */
class RegisterUserHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var PasswordEncoder
     */
    private $passwordEncoder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        UserRepository $userRepository,
        PasswordEncoder $passwordEncoder,
        LoggerInterface $logger
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->logger = $logger;
    }

    public function handle(RegisterUserCommand $command): bool
    {
        $userRole = !empty($command->getRole()) ? UserRole::byName($command->getRole()) : UserRole::USER();

        try {
            $user = User::create(
                UserId::generate(),
                CompanyId::fromString($command->getCompanyId()),
                $command->getEmail(),
                $command->getUsername(),
                $command->getPassword(),
                null,
                UserStatus::CHANGE_PASSWORD(),
                $userRole
            );

            $user->setPassword($this->passwordEncoder->hashPassword($user));

            return $this->userRepository->save($user);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), [__METHOD__]);
        }

        return false;
    }
}
