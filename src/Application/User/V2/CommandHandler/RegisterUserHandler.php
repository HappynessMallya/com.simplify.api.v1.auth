<?php

declare(strict_types=1);

namespace App\Application\User\V2\CommandHandler;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserRole;
use App\Domain\Model\User\UserStatus;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyByUserRepository;
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

    /** @var CompanyByUserRepository  */
    private CompanyByUserRepository $companyByUserRepository;

    /**
     * @param UserRepository $userRepository
     * @param DoctrineCompanyRepository $companyRepository
     * @param PasswordEncoder $passwordEncoder
     * @param SendCredentialsService $sendCredentials
     * @param LoggerInterface $logger
     * @param CompanyByUserRepository $companyByUserRepository
     */
    public function __construct(
        UserRepository $userRepository,
        DoctrineCompanyRepository $companyRepository,
        PasswordEncoder $passwordEncoder,
        SendCredentialsService $sendCredentials,
        LoggerInterface $logger,
        CompanyByUserRepository $companyByUserRepository
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->sendCredentials = $sendCredentials;
        $this->logger = $logger;
        $this->companyByUserRepository = $companyByUserRepository;
    }

    /**
     * @param RegisterUserCommand $command
     * @return array
     * @throws Exception
     */
    public function __invoke(RegisterUserCommand $command): array
    {
        $userRole = !empty($command->getRole()) ? UserRole::byName($command->getRole()) : UserRole::USER();

        try {
            $userId = UserId::generate();
            $password = empty($command->getPassword()) ? base64_encode($this::NEW_PASSWORD) : $command->getPassword();

            $user = User::create(
                $userRole,
                $userId,
                CompanyId::fromString($command->getCompanies()[0]),
                $command->getEmail(),
                $command->getUsername(),
                $password,
                null,
                UserStatus::CHANGE_PASSWORD(),
                UserType::byValue($command->getUserType()),
                $command->getFirstName(),
                $command->getLastName(),
                $command->getMobileNumber()
            );

            $user->setPassword($this->passwordEncoder->hashPassword($user));

            if (!$this->userRepository->save($user)) {
                $this->logger->critical(
                    'The user could not be registered',
                    [
                        'user_id' => $user->userId(),
                        'company_id' => $user->companyId(),
                        'email' => $user->email(),
                        'username' => $user->username(),
                    ]
                );

                throw new Exception(
                    'The user could not be registered',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            $this->companyByUserRepository->saveCompaniesToUser($userId, $command->getCompanies());

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
            }
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to register user',
                [
                    'company_id' => $command->getCompanies()[0],
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
            'createdAt' => (new DateTime())
                ->setTimezone(new DateTimeZone('Africa/Dar_es_Salaam'))
                ->format(('Y-m-d H:i:s')),
        ];
    }
}
