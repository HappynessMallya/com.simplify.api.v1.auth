<?php

declare(strict_types=1);

namespace App\Application\User\V1\CommandHandler;

use App\Application\User\V1\Command\UpdateUserCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserType;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Repository\DoctrineCompanyRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UpdateUserHandler
 * @package App\Application\User\V1\CommandHandler
 */
class UpdateUserHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var DoctrineCompanyRepository */
    private DoctrineCompanyRepository $companyRepository;

    /** @var CompanyByUserRepository */
    private CompanyByUserRepository $companyByUserRepository;

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     * @param DoctrineCompanyRepository $companyRepository
     * @param CompanyByUserRepository $companyByUserRepository
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository,
        DoctrineCompanyRepository $companyRepository,
        CompanyByUserRepository $companyByUserRepository
    ) {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->companyByUserRepository = $companyByUserRepository;
    }

    /**
     * @param UpdateUserCommand $command
     * @return bool
     * @throws Exception
     */
    public function handle(UpdateUserCommand $command): bool
    {
        $isById = false;

        if (!empty($command->getUserType())) {
            $isById = true;
            $userId = UserId::fromString($command->getUserId());
            $userTypeWhoUpdate = UserType::byName($command->getUserType());

            if (
                $userTypeWhoUpdate->sameValueAs(UserType::TYPE_OWNER()) ||
                $userTypeWhoUpdate->sameValueAs(UserType::TYPE_ADMIN())
            ) {
                $user = $this->userRepository->get($userId);
            } else {
                $this->logger->critical(
                    'User who is making the change is neither owner nor admin',
                    [
                        'user_type' => $userTypeWhoUpdate->getValue(),
                        'method' => __METHOD__,
                    ]
                );

                throw new Exception(
                    'User who is making the change is neither owner nor admin: ' . $userTypeWhoUpdate->getValue(),
                    Response::HTTP_BAD_REQUEST
                );
            }
        } else {
            $user = $this->userRepository->getByEmail($command->getUsername());
        }

        $companies = $command->getCompanies();

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

        if (empty($user)) {
            $this->logger->critical(
                'User could not be found',
                [
                    'email' => $command->getEmail(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        $this->companyByUserRepository->removeCompaniesFromUser($user->userId());
        $this->companyByUserRepository->saveCompaniesToUser($user->userId(), $companies);

        if ($isById && (!$user->getUserType()->sameValueAs(UserType::TYPE_OPERATOR()))) {
            $this->logger->critical(
                'User to be updated is not an operator',
                [
                    'email' => $command->getEmail(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User to be updated is not an operator: ' . $user->getUserType()->getValue(),
                Response::HTTP_NOT_FOUND
            );
        }

        $criteria['updatedAt'] = new DateTime('now');

        if (!empty($command->getFirstName())) {
            $criteria['firstName'] = $command->getFirstName();
        }

        if (!empty($command->getLastName())) {
            $criteria['lastName'] = $command->getLastName();
        }

        if (!empty($command->getEmail())) {
            $criteria['username'] = $command->getEmail();
            $criteria['email'] = $command->getEmail();
        }

        if (!empty($command->getMobileNumber())) {
            $criteria['mobileNumber'] = $command->getMobileNumber();
        }

        if (count($criteria) === 1) {
            $this->logger->critical(
                'You need at least one field to update user',
                [
                    'email' => $command->getEmail(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'You need at least one field to update user',
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($user->email() !== $command->getEmail()) {
            $isPreRegistered = $this->userRepository->findOneBy(
                [
                    'email' => $command->getEmail(),
                ]
            );
        }

        if (!empty($isPreRegistered)) {
            $this->logger->critical(
                'Another user has pre-registered with the email provided',
                [
                    'email' => $command->getEmail(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'Another user has pre-registered with the email provided',
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $user->update($criteria);
            $isUpdated = $this->userRepository->save($user);
        } catch (Exception $exception) {
            $this->logger->critical(
                'User could not be updated',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User could not be updated. ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($isUpdated) {
            $this->logger->debug(
                'User updated successfully',
                [
                    'first_name' => $user->firstName(),
                    'last_name' => $user->lastName(),
                    'username' => $user->email(),
                    'email' => $user->email(),
                    'mobile_number' => $user->mobileNumber(),
                ]
            );

            return true;
        }

        return false;
    }
}
