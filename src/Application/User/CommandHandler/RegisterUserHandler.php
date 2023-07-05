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
use DateTime;
use DateTimeZone;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RegisterUserHandler
 * @package App\Application\User\CommandHandler
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

    /**
     * @param UserRepository $userRepository
     * @param DoctrineCompanyRepository $companyRepository
     * @param PasswordEncoder $passwordEncoder
     * @param SendCredentialsService $sendCredentials
     * @param LoggerInterface $logger
     */
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
     * @return array
     * @throws Exception
     */
    public function handle(RegisterUserCommand $command): array
    {
        try {
            $userId = UserId::generate();
            $companyId = CompanyId::fromString($command->getCompanyId());
            $userRole = empty($command->getRole()) ? UserRole::USER() : UserRole::byName($command->getRole());
            $password = empty($command->getPassword()) ? base64_encode($this::NEW_PASSWORD) : $command->getPassword();

            $company = $this->companyRepository->get($companyId);

            if (empty($company)) {
                $this->logger->critical(
                    'Company could not be found',
                    [
                        'company_id' => $companyId->toString(),
                        'method' => __METHOD__,
                    ]
                );

                throw new Exception(
                    'Company could not be found',
                    Response::HTTP_NOT_FOUND
                );
            }

            $user = User::create(
                $userRole,
                $userId,
                $companyId,
                $command->getEmail(),
                $command->getUsername(),
                $password,
                null,
                UserStatus::CHANGE_PASSWORD(),
                UserRole::USER(),
                UserType::TYPE_OPERATOR(),
                $command->getFirstName(),
                $command->getLastName(),
                $command->getMobileNumber()
            );

            $user->setPassword($this->passwordEncoder->hashPassword($user));

            $isSaved = $this->userRepository->save($user);

            if (!$isSaved) {
                $this->logger->critical(
                    'The user could not be registered',
                    [
                        'company_id' => $user->companyId(),
                        'user_id' => $user->userId(),
                        'username' => $user->username(),
                        'email' => $user->email(),
                        'method' => __METHOD__,
                    ]
                );

                throw new Exception(
                    'The user could not be registered',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

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
            }
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred',
                [
                    'company_id' => $command->getCompanyId(),
                    'username' => $command->getUsername(),
                    'error_message' => $exception->getMessage(),
                    'error_code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return [
            'userId' => $userId->toString(),
            'username' => $user->username(),
            'createdAt' => (
                new DateTime('now', new DateTimeZone('Africa/Dar_es_Salaam'))
            )->format('Y-m-d H:i:s'),
        ];
    }
}
