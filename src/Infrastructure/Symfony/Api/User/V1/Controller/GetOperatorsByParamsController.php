<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V1\Controller;

use App\Application\User\V1\Query\GetOperatorsByParamsQuery;
use App\Application\User\V1\QueryHandler\GetOperatorsByParamsHandler;
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
 * Class GetOperatorsByParamsController
 * @package App\Infrastructure\Symfony\Api\User\V1\Controller
 */
class GetOperatorsByParamsController extends BaseController
{
    /**
     * @Route(path="/operators/query", methods={"GET"})
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param Request $request
     * @param GetOperatorsByParamsHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getOperatorsByParamsAction(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        Request $request,
        GetOperatorsByParamsHandler $handler
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $organizationId = $tokenData['organizationId'];
        $userId = $tokenData['userId'];
        $userType = $tokenData['userType'];

        $firstName = $request->query->get('firstName');
        $lastName = $request->query->get('lastName');
        $email = $request->query->get('email');
        $mobileNumber = $request->query->get('mobileNumber');
        $status = $request->query->get('status');

        $query = new GetOperatorsByParamsQuery(
            $organizationId,
            $userId,
            $userType,
            $firstName ?? '',
            $lastName ?? '',
            $email ?? '',
            $mobileNumber ?? '',
            $status ?? 'ALL',
        );

        try {
            $operators = $handler->__invoke($query);

            if (!$operators) {
                $this->logger->debug(
                    'No operators found by the search criteria',
                    [
                        'criteria' => [
                            'userId' => $userId,
                            'userType' => $userType,
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'email' => $email,
                            'mobileNumber' => $mobileNumber,
                            'status' => $status,
                        ],
                    ]
                );

                return $this->createApiResponse(
                    [
                        'success' => false,
                        'message' => 'No operators found by the search criteria',
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        } catch (Exception $exception) {
            $this->logger->critical(
                'Error trying to find the set of operators',
                [
                    'user_id' => $userId,
                    'user_type' => $userType,
                    'error_message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error_message' => 'Error trying to find the set of operators: ' . $exception->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            $operators,
            Response::HTTP_OK
        );
    }
}
