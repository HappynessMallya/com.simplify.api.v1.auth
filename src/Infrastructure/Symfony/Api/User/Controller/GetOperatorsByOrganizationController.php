<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller;

use App\Application\User\Query\GetOperatorsByOrganizationQuery;
use App\Application\User\QueryHandler\GetOperatorsByOrganizationHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GetOperatorsByOrganizationController
 * @package App\Infrastructure\Symfony\Api\ApiUser\Controller\V2
 */
class GetOperatorsByOrganizationController extends BaseController
{
    /**
     * @Route(path="/operators", methods={"GET"})
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param GetOperatorsByOrganizationHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getOperatorsByOrganizationIdAction(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        GetOperatorsByOrganizationHandler $handler
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $organizationId = $tokenData['organizationId'];
        $userType = $tokenData['userType'];

        $query = new GetOperatorsByOrganizationQuery($organizationId, $userType);

        try {
            $operators = $handler->__invoke($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to get operators',
                [
                    'error_message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Exception error trying to get operators. ' . $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (empty($operators)) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Internal server error trying to get operators',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'organizationId' => $organizationId,
                'operators' => $operators,
            ],
            Response::HTTP_OK
        );
    }
}
