<?php

declare(strict_types=1);

namespace App\Application\User\V1\CommandHandler;

use App\Application\User\V1\Command\UpdateUserCommand;
use App\Domain\Repository\UserRepository;
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

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository
    ) {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
    }

    /**
     * @param UpdateUserCommand $command
     * @return bool
     * @throws Exception
     */
    public function handle(UpdateUserCommand $command): bool
    {
        $user = $this->userRepository->getByEmail($command->getEmail());

        if (empty($user)) {
            $this->logger->critical(
                'User could not be found by email',
                [
                    'email' => $command->getEmail(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User could not be found by email: ' . $command->getEmail(),
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

        $isPreRegistered = $this->userRepository->findOneBy(
            [
                'email' => $command->getEmail(),
            ]
        );

        if (!empty($isPreRegistered)) {
            $this->logger->critical(
                'User has pre-registered with the email provided',
                [
                    'email' => $command->getEmail(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User has pre-registered with the email provided',
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
