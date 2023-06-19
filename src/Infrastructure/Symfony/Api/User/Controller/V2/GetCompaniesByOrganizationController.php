<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller\V2;

use App\Application\User\V2\QueryHandler\GetCompaniesByOrganizationHandler;
use App\Application\User\V2\QueryHandler\GetCompaniesByOrganizationQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GetCompaniesByOrganizationController
 * @package App\Infrastructure\Symfony\Api\ApiUser\Controller\V2
 */
class GetCompaniesByOrganizationController extends BaseController
{
    /**
     * @Route(path="/companies", methods={"GET"})
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param GetCompaniesByOrganizationHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getCompaniesByUserTypeAction(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        GetCompaniesByOrganizationHandler $handler
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $organizationId = $tokenData['organizationId'];
        $userId = $tokenData['userId'];
        $userType = $tokenData['userType'];

        $query = new GetCompaniesByOrganizationQuery($userId, $userType);

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

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Exception error trying to get companies. ' . $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (empty($companies)) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Internal server error trying to get companies',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'userId' => $userId,
                'organizationId' => $organizationId,
                'companies' => $companies,
            ],
            Response::HTTP_OK
        );
    }
}
