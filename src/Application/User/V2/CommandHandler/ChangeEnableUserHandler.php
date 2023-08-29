<?php

declare(strict_types=1);

namespace App\Application\User\V2\CommandHandler;

use App\Application\Company\V1\Command\ChangeEnableCompanyCommand;
use App\Application\User\V2\Command\ChangeEnableUserCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\UserId;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChangeEnableUserHandler
 * @package App\Application\User\V2\CommandHandler
 */
class ChangeEnableUserHandler
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
     * @param ChangeEnableUserCommand $command
     * @throws Exception
     */
    public function handle(ChangeEnableUserCommand $command): void
    {
        $userId = UserId::fromString($command->getUserId());
        $user = $this->userRepository->get($userId);

        if (empty($user)) {
            $this->logger->critical(
                'user could not be found',
                [
                    'user_id' => $userId->toString(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'User could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        if ($user->isEnabled() != $command->isEnable()) {
            $user->setEnable($command->isEnable());
            $user->setUpdatedAt(new DateTime());
        }

        try {
            $this->userRepository->save($user);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred when trying change enable company',
                [
                    'user_id' => $userId->toString(),
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'An internal server error has been occurred when trying change enable company',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
