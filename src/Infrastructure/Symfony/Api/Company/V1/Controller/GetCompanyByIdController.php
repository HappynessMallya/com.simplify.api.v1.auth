<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\Controller;

use App\Application\Company\Query\GetCompanyByIdQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetCompanyByIdController
 * @package App\Infrastructure\Symfony\Api\Company\V1\Controller
 */
class GetCompanyByIdController extends BaseController
{
    /**
     * @Route(path="/{companyId}", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompanyByIdAction(
        Request $request
    ): JsonResponse {
        $companyId = $request->get('companyId');

        $query = new GetCompanyByIdQuery($companyId);

        try {
            $company =  $this->commandBus->handle($query);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to get company',
                [
                    'error_message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Exception error trying to get company. ' . $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
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
            [
                'companyId' => $company->companyId()->toString(),
                'organizationId' => $company->organizationId()->toString(),
                'name' => $company->name(),
                'tin' => $company->tin(),
                'email' => $company->email(),
                'address' => $company->address(),
                'traRegistration' => $company->traRegistration(),
                'createdAt' => $company->createdAt()->format('Y-m-d H:i:s'),
                'status' => $company->companyStatus(),
            ],
            Response::HTTP_OK
        );
    }
}
