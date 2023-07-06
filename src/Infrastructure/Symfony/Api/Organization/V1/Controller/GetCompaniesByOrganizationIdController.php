<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\V1\Controller;

use App\Application\Organization\QueryHandler\GetCompaniesByOrganizationIdHandler;
use App\Application\Organization\QueryHandler\GetCompaniesByOrganizationIdQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GetCompaniesByOrganizationIdController
 * @package App\Infrastructure\Symfony\Api\Organization\Controller
 */
class GetCompaniesByOrganizationIdController extends BaseController
{
    /**
     * @Route(path="/{organizationId}/companies", methods={"GET"})
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param Request $request
     * @param LoggerInterface $logger
     * @param GetCompaniesByOrganizationIdHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getCompaniesAction(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        Request $request,
        LoggerInterface $logger,
        GetCompaniesByOrganizationIdHandler $handler
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $organizationId = $request->get('organizationId');
        $userType = $tokenData['userType'];

        $query = new GetCompaniesByOrganizationIdQuery(
            $organizationId,
            $userType
        );

        try {
            $companies = $handler->__invoke($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to get companies',
                [
                    'organization_id' => $organizationId,
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
                'organizationId' => $organizationId,
                'companies' => $companies,
            ],
            Response::HTTP_OK
        );
    }
}
