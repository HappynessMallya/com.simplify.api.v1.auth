<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller;

use App\Application\Company\Query\GetCompanyByIdQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetCompanyByIdController
 * @package App\Infrastructure\Symfony\Api\Company\Controller
 */
class GetCompanyByIdController extends BaseController
{
    /**
     * @Route(path="/{companyId}", methods={"GET"})
     *
     * @param string $companyId
     * @return JsonResponse
     */
    public function getCompaniesAction(string $companyId)
    {
        $command = new GetCompanyByIdQuery($companyId);
        $company =  $this->commandBus->handle($command);

        if (empty($company)) {
            return $this->createApiResponse(['errors' => 'Not Found'], Response::HTTP_NOT_FOUND);
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
            ],
            Response::HTTP_OK
        );
    }
}
