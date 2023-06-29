<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller\V2;

use App\Application\Company\V2\CommandHandler\UpdateCompanyCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\Controller\V2\FormType\UpdateCompanyType;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateCompanyController
 * @package App\Infrastructure\Symfony\Api\Company\Controller
 */
class UpdateCompanyController extends BaseController
{
    /**
     * @Route(path="/", methods={"PUT"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function action(Request $request): JsonResponse
    {
        $command = new UpdateCompanyCommand();
        $form = $this->createForm(UpdateCompanyType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'errors' => $this->getValidationErrors($form)
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $response = null;
        try {
            $response = $this->commandBus->handle($command);
        } catch (\Exception $e) {
            $this->logger->critical(
                $e->getMessage(),
                [

                    'method' => __METHOD__
                ]
            );

            if ($e->getCode() === 404) {
                return $this->createApiResponse(
                    [
                        'success' => false,
                        'errors' => $e->getMessage()
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->createApiResponse(
                [
                    'success' => false,
                    'errors' => $e->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'companyId' => $response['companyId'],
                'updatedAt' => $response['updatedAt'],
            ],
            Response::HTTP_OK
        );
    }
}
