<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\Controller;

use App\Application\Organization\QueryHandler\ChangeOrganizationStatusByIdHandler;
use App\Application\Organization\QueryHandler\ChangeOrganizationStatusByIdQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ChangeOrganizationStatusByIdController
 * @package App\Infrastructure\Symfony\Api\Organization\Controller
 */
class ChangeOrganizationStatusByIdController extends BaseController
{
    /**
     * @Route(path="/{organizationId}", methods={"PUT"})
     *
     * @param Request $request
     * @param ChangeOrganizationStatusByIdHandler $handler
     * @return JsonResponse
     */
    public function changeOrganizationStatusByIdAction(
        Request $request,
        ChangeOrganizationStatusByIdHandler $handler
    ): JsonResponse {
        $organizationId = $request->get('organizationId');
        $newStatus = $request->get('newStatus');

        $query = new ChangeOrganizationStatusByIdQuery($organizationId, $newStatus);

        try {
            $isStatusChanged = $handler->__invoke($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to change organization status',
                [
                    'error_message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Exception error trying to change organization status. ' . $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$isStatusChanged) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Organization status not changed',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'message' => 'Organization status changed',
            ],
            Response::HTTP_OK
        );
    }
}
