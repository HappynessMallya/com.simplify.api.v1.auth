<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller\V2;

use App\Application\User\V2\QueryHandler\GetCompaniesByUserTypeHandler;
use App\Application\User\V2\QueryHandler\GetCompaniesByUserTypeQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GetCompaniesByUserTypeController
 * @package App\Infrastructure\Symfony\Api\ApiUser\Controller\V2
 */
class GetCompaniesByUserTypeController extends BaseController
{
    /**
     * @Route(path="/companies", methods={"GET"})
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param GetCompaniesByUserTypeHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getCompanioesByUserTypeAction(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        GetCompaniesByUserTypeHandler $handler
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $userId = $tokenData['userId'];
        $userType = $tokenData['userType'];

        $query = new GetCompaniesByUserTypeQuery($userId, $userType);
        $companies = null;

        try {
            $companies = $handler->__invoke($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to get companies',
                [
                    'error_message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Exception error trying to get companies. ' . $exception->getMessage(),
                ]
            );
        }

        if (empty($companies)) {
            return $this->createApiResponse(
                [
                    'error' => 'Internal server error trying to get companies',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'user_id' => $userId,
                'companies' => $companies,
            ],
            Response::HTTP_OK
        );
    }
}
