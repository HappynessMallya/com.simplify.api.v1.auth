<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\RegisterUserCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserRole;
use App\Domain\Model\User\UserStatus;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\UserRepository;
use App\Domain\Services\SendCredentialsRequest;
use App\Domain\Services\User\PasswordEncoder;
use App\Domain\Services\SendCredentialsService;
use App\Infrastructure\Repository\DoctrineCompanyRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

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
     * @throws Exception
     */
    public function handle(RegisterUserCommand $command): bool
    {
        $userRole = empty($command->getRole()) ? UserRole::USER() : UserRole::byName($command->getRole());

        try {
            $password = empty($command->getPassword()) ? base64_encode($this::NEW_PASSWORD) : $command->getPassword();

            $user = User::create(
                $userRole,
                UserId::generate(),
                CompanyId::fromString($command->getCompanyId()),
                $command->getEmail(),
                $command->getUsername(),
                $password,
                null,
                UserStatus::CHANGE_PASSWORD(),
                UserType::TYPE_OPERATOR(),
                $command->getFirstName(),
                $command->getLastName(),
                $command->getMobileNumber()
            );

            $user->setPassword($this->passwordEncoder->hashPassword($user));

            if (!$this->userRepository->save($user)) {
                $this->logger->critical(
                    'Error trying to save user',
                    [
                        'company_id' => $user->companyId(),
                        'user_id' => $user->userId(),
                        'username' => $user->username(),
                        'email' => $user->email(),
                        'method' => __METHOD__,
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
                $company->name()
            );

            $response = $this->sendCredentials->onSendCredentials($request);

            if (!$response->isSuccess()) {
                $this->logger->critical(
                    'Error trying to send credentials to client',
                    [
                        'company_id' => $company->companyId()->toString(),
                        'username' => $user->username(),
                        'error_message' => $response->getErrorMessage(),
                        'method' => __METHOD__,
                    ]
                );

                return false;
            }
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to register user',
                [
                    'company_id' => $command->getCompanyId(),
                    'username' => $command->getUsername(),
                    'error_message' => $exception->getMessage(),
                    'error_code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Exception error trying to register user: ' . $exception->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }

        return true;
    }
}
