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
use App\Domain\Services\SendCredentialsRequest;
use App\Domain\Services\User\PasswordEncoder;
use App\Domain\Services\SendCredentialsService;
use App\Infrastructure\Repository\DoctrineCompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class RegisterUserHandler
 * @package App\Application\ApiUser\CommandHandler
 */
class RegisterUserHandler
{
    public const NEW_PASSWORD = 'tanzaniaSimplify123*';

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var DoctrineCompanyRepository */
    private DoctrineCompanyRepository $companyRepository;

    /** @var PasswordEncoder */
    private PasswordEncoder $passwordEncoder;

    /** @var SendCredentialsService */
    private SendCredentialsService $sendCredentials;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    public function __construct(
        UserRepository $userRepository,
        DoctrineCompanyRepository $companyRepository,
        PasswordEncoder $passwordEncoder,
        SendCredentialsService $sendCredentials,
        LoggerInterface $logger
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->sendCredentials = $sendCredentials;
        $this->logger = $logger;
    }

    /**
     * @param RegisterUserCommand $command
     * @return bool
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function handle(RegisterUserCommand $command): bool
    {
        $userRole = !empty($command->getRole()) ? UserRole::byName($command->getRole()) : UserRole::USER();

        try {
            $password = empty($command->getPassword()) ? base64_encode($this::NEW_PASSWORD) : $command->getPassword();

            $user = User::create(
                UserId::generate(),
                CompanyId::fromString($command->getCompanyId()),
                $command->getEmail(),
                $command->getUsername(),
                $password,
                null,
                UserStatus::CHANGE_PASSWORD(),
                $userRole
            );

            $user->setPassword($this->passwordEncoder->hashPassword($user));

            if (!$this->userRepository->save($user)) {
                $this->logger->critical(
                    'Error trying to save user',
                    [
                        'userId' => $user->userId(),
                        'company_id' => $user->companyId(),
                        'email' => $user->email(),
                        'username' => $user->username(),
                    ]
                );

                return false;
            }

            $company = $this->companyRepository->get($user->companyId());

            $request = new SendCredentialsRequest(
                'NEW_CREDENTIALS',
                $user->username(),
                $password,
                $user->email(),
                $company->companyId()->toString()
            );

            $response = $this->sendCredentials->onSendCredentials($request);

            if (!$response->isSuccess()) {
                $this->logger->critical(
                    'Error trying to send credentials to client',
                    [
                        'error_message' => $response->getErrorMessage(),
                        'username' => $user->username(),
                        'company_id' => $company->companyId()->toString(),
                        'method' => __METHOD__,
                    ]
                );
            }
        } catch (Exception $e) {
            $this->logger->critical(
                'An error has been occurred trying to create user',
                [
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'username' => $command->getUsername(),
                    'company_id' => $command->getCompanyId(),
                    'method' => __METHOD__,
                ]
            );

            return false;
        }

        return true;
    }
}
