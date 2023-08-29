<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\Controller;

use App\Application\Company\V1\Query\GetCompanyBySerialQuery;
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
class GetCompanyBySerialController extends BaseController
{
    /**
     * @Route(path="/serial/{serial}", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompanyBySerialAction(
        Request $request
    ): JsonResponse {
        $serial = $request->get('serial');
        $query = new GetCompanyBySerialQuery($serial);

        try {
            $company =  $this->commandBus->handle($query);
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
            $company,
            Response::HTTP_OK
        );
    }
}
