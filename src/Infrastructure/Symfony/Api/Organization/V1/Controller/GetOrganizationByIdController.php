<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\V1\Controller;

use App\Application\Organization\V1\Query\GetOrganizationByIdQuery;
use App\Application\Organization\V1\QueryHandler\GetOrganizationByIdHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetOrganizationByIdController
 * @package App\Infrastructure\Symfony\Api\Organization\V1\Controller
 */
class GetOrganizationByIdController extends BaseController
{
    /**
     * @Route(path="/{organizationId}", methods={"GET"})
     *
     * @param Request $request
     * @param GetOrganizationByIdHandler $handler
     * @return JsonResponse
     */
    public function getOrganizationByIdAction(
        Request $request,
        GetOrganizationByIdHandler $handler
    ): JsonResponse {
        $organizationId = $request->get('organizationId');
        $query = new GetOrganizationByIdQuery($organizationId);

        try {
            $organization = $handler->__invoke($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to get organization details',
                [
                    'error_message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Exception error trying to get organization details. ' . $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->createApiResponse(
            [
                'organization' => $organization,
            ],
            Response::HTTP_OK
        );
    }
}
