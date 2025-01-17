<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V1\Controller;

use App\Application\User\V1\Query\GetProfileByUsernameQuery;
use App\Domain\Model\User\User;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GetProfileByUsernameController
 * @package App\Infrastructure\Symfony\Api\User\V1\Controller
 */
class GetProfileByUsernameController extends BaseController
{
    /**
     * @Route(path="/profile", methods={"GET"})
     *
     * @param TokenStorageInterface $jwtStorage
     * @param JWTTokenManagerInterface $jwtManager
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getProfileByUsernameAction(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $username = $tokenData['username'];

        /** @var User $user */
        $user = null;

        $query = new GetProfileByUsernameQuery($username);

        try {
            $user = $this->commandBus->handle($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to get user',
                [
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            if ($exception->getCode() === Response::HTTP_NOT_FOUND) {
                return $this->createApiResponse(
                    [
                        'errors' => 'Exception error trying to get user. ' . $exception->getMessage(),
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        }

        if (empty($user)) {
            return $this->createApiResponse(
                [
                    'errors' => 'Internal server error trying to get user',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'userId' => $user->userId()->toString(),
                'firstName' => $user->firstName(),
                'lastName' => $user->lastName(),
                'username' => $user->username(),
                'email' => $user->email(),
                'mobileNumber' => empty($user->mobileNumber()) ? '' : $user->mobileNumber(),
                'userType' => $user->getUserType()->getValue(),
                'status' => $user->status()->getValue(),
                'createdAt' => $user->createdAt()->format('Y-m-d H:i:s'),
            ],
            Response::HTTP_OK
        );
    }
}
