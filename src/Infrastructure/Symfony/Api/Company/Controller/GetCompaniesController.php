<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller;

use App\Application\Company\Query\GetCompaniesQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetCompaniesController
 * @package App\Infrastructure\Symfony\Api\Company\Controller
 */
class GetCompaniesController extends BaseController
{
    /**
     * @Route(path="/", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompaniesAction(Request $request)
    {
        $companies = null;

        try {
            $command = new GetCompaniesQuery();
            $command->setPage((int)$request->get('page') ?? 0);
            $command->setPageSize((int)$request->get('page_size') ?? 10);
            $companies = $this->commandBus->handle($command);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), [__METHOD__]);
        }

        return $this->createApiResponse($companies, Response::HTTP_OK);
    }
}
