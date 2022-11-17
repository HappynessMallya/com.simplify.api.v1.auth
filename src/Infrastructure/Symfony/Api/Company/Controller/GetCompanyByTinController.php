<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller;

use App\Application\Company\Query\GetCompanyByTinQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetCompanyByTinController
 * @package App\Infrastructure\Symfony\Api\Company\Controller
 */
class GetCompanyByTinController extends BaseController
{
    /**
     * @Route(path="/tin/{tinId}", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function action(Request $request): JsonResponse
    {
        $query = new GetCompanyByTinQuery($request->get('tinId'));
        $company =  $this->commandBus->handle($query);

        if (empty($company)) {
            return $this->createApiResponse(
                [
                    'errors' => 'Company could not be found',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->createApiResponse(
            [
                'companyId' => $company->companyId()->toString(),
                'name' => $company->name(),
                'tin' => $company->tin(),
                'email' => $company->email(),
                'address' => $company->address(),
                'traRegistration' => $company->traRegistration(),
                'createdAt' => $company->createdAt()->format(DATE_ATOM),
                'status' => $company->companyStatus(),
            ],
            Response::HTTP_OK
        );
    }
}
