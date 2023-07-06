<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\V1\Controller;

use App\Application\Organization\V1\Query\GetOrganizationsByParamsQuery;
use App\Application\Organization\V1\QueryHandler\GetOrganizationsByParamsHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetOrganizationsByParamsController
 * @package App\Infrastructure\Symfony\Api\Organization\V1\Controller
 */
class GetOrganizationsByParamsController extends BaseController
{
    /**
     * @Route(path="/getOrganizationsBy/query", methods={"GET"})
     *
     * @param Request $request
     * @param GetOrganizationsByParamsHandler $handler
     * @return JsonResponse
     */
    public function getOrganizationByParamsAction(
        Request $request,
        GetOrganizationsByParamsHandler $handler
    ): JsonResponse {
        $name = $request->query->get('name');
        $ownerName = $request->query->get('ownerName');
        $ownerEmail = $request->query->get('ownerEmail');
        $ownerPhoneNumber = $request->query->get('ownerPhoneNumber');
        $status = $request->query->get('status');

        $query = new GetOrganizationsByParamsQuery(
            $name ?? '',
            $ownerName ?? '',
            $ownerEmail ?? '',
            $ownerPhoneNumber ?? '',
            $status ?? 'ALL',
        );

        try {
            $organizations = $handler->__invoke($query);

            if (!$organizations) {
                $this->logger->debug(
                    'No organizations found by the search criteria',
                    [
                        'criteria' => [
                            'name' => $name,
                            'ownerName' => $ownerName,
                            'ownerEmail' => $ownerEmail,
                            'ownerPhoneNumber' => $ownerPhoneNumber,
                            'status' => $status,
                        ],
                    ]
                );

                return $this->createApiResponse(
                    [
                        'success' => false,
                        'error' => 'No organizations found by the search criteria',
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        } catch (Exception $exception) {
            $this->logger->critical(
                'Error trying to find the set of organizations',
                [
                    'code' => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Error trying to find the set of organizations. ' . $exception->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            $organizations,
            Response::HTTP_OK
        );
    }
}
