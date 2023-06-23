<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller\V2;

use App\Application\User\V2\QueryHandler\ChangeUserStatusByIdHandler;
use App\Application\User\V2\QueryHandler\ChangeUserStatusByIdQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ChangeUserStatusByIdController
 * @package App\Infrastructure\Symfony\Api\ApiUser\Controller\V2
 */
class ChangeUserStatusByIdController extends BaseController
{
    /**
     * @Route(path="/operator/{userId}", methods={"PUT"})
     *
     * @param Request $request
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param ChangeUserStatusByIdHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function changeUserStatusByIdAction(
        Request $request,
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        ChangeUserStatusByIdHandler $handler
    ): JsonResponse {
        $userId = $request->get('userId');
        $newStatus = $request->get('newStatus');
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $userType = $tokenData['userType'];

        if (empty($newStatus)) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'New status is mandatory',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $query = new ChangeUserStatusByIdQuery($userId, $userType, $newStatus);

        try {
            $isStatusChanged = $handler->__invoke($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to change user status',
                [
                    'error_message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Exception error trying to change user status. ' . $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$isStatusChanged) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'User status has not changed',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'message' => 'User status changed',
            ],
            Response::HTTP_OK
        );
    }
}
