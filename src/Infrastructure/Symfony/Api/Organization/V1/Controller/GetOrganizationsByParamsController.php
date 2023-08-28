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
     * @Route(path="/getBy/query", methods={"GET"})
     *
     * @param Request $request
     * @param GetOrganizationsByParamsHandler $handler
     * @return JsonResponse
     */
    public function getOrganizationsByParamsAction(
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
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
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
            $organizations,
            Response::HTTP_OK
        );
    }
}
