<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\Controller;

use App\Application\Company\V1\Query\GetCompanyByTinQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetCompanyByTinController
 * @package App\Infrastructure\Symfony\Api\Company\V1\Controller
 */
class GetCompanyByTinController extends BaseController
{
    /**
     * @Route(path="/tin/{tin}", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompanyByTinAction(
        Request $request
    ): JsonResponse {
        $tin = $request->get('tin');

        $query = new GetCompanyByTinQuery($tin);

        try {
            $company =  $this->commandBus->handle($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred',
                [
                    'error' => $exception->getMessage(),
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

        if (empty($company)) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'errors' => 'Company could not be found',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->createApiResponse(
            $company,
            Response::HTTP_OK
        );
    }
}
