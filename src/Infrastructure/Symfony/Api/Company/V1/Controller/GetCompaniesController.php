<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\Controller;

use App\Application\Company\Query\GetCompaniesQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetCompaniesController
 * @package App\Infrastructure\Symfony\Api\Company\V1\Controller
 */
class GetCompaniesController extends BaseController
{
    /**
     * @Route(path="/", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompaniesAction(
        Request $request
    ): JsonResponse {
        $query = new GetCompaniesQuery();
        $query->setPage((int)$request->get('page') ?? 0);
        $query->setPageSize((int)$request->get('page_size') ?? 10);

        try {
            $companies = $this->commandBus->handle($query);
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
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            $companies,
            Response::HTTP_OK
        );
    }
}
