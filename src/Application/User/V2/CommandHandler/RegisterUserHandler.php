<?php

declare(strict_types=1);

namespace App\Application\User\V2\CommandHandler;

use App\Application\User\V2\Command\RegisterUserCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserRole;
use App\Domain\Model\User\UserStatus;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Services\SendCredentialsRequest;
use App\Domain\Services\SendCredentialsService;
use App\Domain\Services\User\PasswordEncoder;
use App\Infrastructure\Repository\DoctrineCompanyRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RegisterUserHandler
 * @package App\Application\User\V2\CommandHandler
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

    /** @var CompanyByUserRepository */
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
    public function handle(RegisterUserCommand $command): array
    {
        try {
            $userIdWhoRegister = UserId::fromString($command->getUserIdWhoRegister());
            $userTypeWhoRegister = UserType::byName($command->getUserTypeWhoRegister());

            if (
                $userTypeWhoRegister->sameValueAs(UserType::TYPE_OWNER()) ||
                $userTypeWhoRegister->sameValueAs(UserType::TYPE_ADMIN())
            ) {
                $user = $this->userRepository->get($userIdWhoRegister);
            } else {
                $this->logger->critical(
                    'User who is making the registration is neither owner nor admin',
                    [
                        'user_type' => $userTypeWhoRegister->getValue(),
                        'method' => __METHOD__,
                    ]
                );

                throw new Exception(
                    'User who is making the registration is neither owner nor admin: ' .
                        $userTypeWhoRegister->getValue(),
                    Response::HTTP_BAD_REQUEST
                );
            }

            if (empty($user)) {
                $this->logger->critical(
                    'User who register could not be found',
                    [
                        'user_id' => $userIdWhoRegister->toString(),
                        'method' => __METHOD__,
                    ]
                );

                throw new Exception(
                    'User who register could not be found',
                    Response::HTTP_NOT_FOUND
                );
            }

            $userId = UserId::generate();
            $companies = $command->getCompanies();
            $userRole = empty($command->getRole()) ? UserRole::USER() : UserRole::byName($command->getRole());
            $password = empty($command->getPassword()) ? base64_encode($this::NEW_PASSWORD) : $command->getPassword();

            foreach ($companies as $providedCompanyId) {
                $companyId = CompanyId::fromString($providedCompanyId);
                $company = $this->companyRepository->get($companyId);

                if (empty($company)) {
                    $this->logger->critical(
                        'Company could not be found',
                        [
                            'company_id' => $providedCompanyId,
                            'method' => __METHOD__,
                        ]
                    );

                    throw new Exception(
                        'At least one company could not be found',
                        Response::HTTP_NOT_FOUND
                    );
                }
            }

            $companyId = CompanyId::fromString($companies[0]);
            $company = $this->companyRepository->get($companyId);

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
                UserType::byValue($command->getUserType()),
                $command->getFirstName(),
                $command->getLastName(),
                $command->getMobileNumber()
            );

            $user->setPassword($this->passwordEncoder->hashPassword($user));

            $isSaved = $this->userRepository->save($user);

            if (!$isSaved) {
                $this->logger->critical(
                    'User could not be registered',
                    [
                        'company_id' => $user->companyId(),
                        'user_id' => $user->userId(),
                        'username' => $user->username(),
                        'email' => $user->email(),
                        'method' => __METHOD__,
                    ]
                );

                throw new Exception(
                    'User could not be registered',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            $this->companyByUserRepository->saveCompaniesToUser($userId, $companies);

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
            'createdAt' => (
                new DateTime('now', new DateTimeZone('Africa/Dar_es_Salaam'))
            )->format('Y-m-d H:i:s'),
        ];
    }
}
