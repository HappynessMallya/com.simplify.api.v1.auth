<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\V1\Controller;

use App\Application\Organization\V1\Query\GetCompaniesByOrganizationIdQuery;
use App\Application\Organization\V1\QueryHandler\GetCompaniesByOrganizationIdHandler;
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
 * Class GetCompaniesByOrganizationController
 * @package App\Infrastructure\Symfony\Api\Organization\V1\Controller
 */
class GetCompaniesByOrganizationIdController extends BaseController
{
    /**
     * @Route(path="/{organizationId}/companies", methods={"GET"})
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param Request $request
     * @param GetCompaniesByOrganizationIdHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getCompaniesByOrganizationIdAction(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        Request $request,
        GetCompaniesByOrganizationIdHandler $handler
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $organizationId = $request->get('organizationId');
        $userType = $tokenData['userType'];

        $query = new GetCompaniesByOrganizationIdQuery($organizationId, $userType);

        try {
            $companies = $handler->__invoke($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred',
                [
                    'organization_id' => $organizationId,
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'An internal server error has been occurred. ' . $exception->getMessage(),
                ],
                $exception->getCode()
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
