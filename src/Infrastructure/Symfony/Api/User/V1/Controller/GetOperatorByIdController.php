<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V1\Controller;

use App\Application\User\Query\GetOperatorByIdQuery;
use App\Application\User\QueryHandler\GetOperatorByIdHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GetOperatorByIdController
 * @package App\Infrastructure\Symfony\Api\ApiUser\Controller\V2
 */
class GetOperatorByIdController extends BaseController
{
    /**
     * @Route(path="/operator/{operatorId}", methods={"GET"})
     *
     * @param Request $request
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param GetOperatorByIdHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getOperatorByIdAction(
        Request $request,
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        GetOperatorByIdHandler $handler
    ): JsonResponse {
        $operatorId = $request->get('operatorId');
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $userType = $tokenData['userType'];

        $query = new GetOperatorByIdQuery($operatorId, $userType);

        try {
            $operator = $handler->__invoke($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to get operator details',
                [
                    'error_message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Exception error trying to get operator details. ' . $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (empty($operator)) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Internal server error trying to get operator details',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            $operator,
            Response::HTTP_OK
        );
    }
}
