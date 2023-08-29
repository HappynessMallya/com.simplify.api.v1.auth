<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\V1\Controller;

use App\Application\Organization\V1\Query\GetCompaniesByOrganizationIdQuery;
use App\Application\Organization\V1\QueryHandler\GetCompaniesByOrganizationIdHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetCompaniesByOrganizationIdController
 * @package App\Infrastructure\Symfony\Api\Organization\V1\Controller
 */
class GetCompaniesByOrganizationIdController extends BaseController
{
    /**
     * @Route(path="/{organizationId}/companies", methods={"GET"})
     *
     * @param Request $request
     * @param string $organizationId
     * @param GetCompaniesByOrganizationIdHandler $handler
     * @return JsonResponse
     */
    public function getCompaniesByOrganizationIdAction(
        Request $request,
        string $organizationId,
        GetCompaniesByOrganizationIdHandler $handler
    ): JsonResponse {
        $organizationId = $request->get('organizationId');

        $query = new GetCompaniesByOrganizationIdQuery($organizationId);

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
