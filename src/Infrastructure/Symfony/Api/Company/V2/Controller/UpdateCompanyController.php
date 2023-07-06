<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V2\Controller;

use App\Application\Company\V2\Command\UpdateCompanyCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\V2\FormType\UpdateCompanyType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateCompanyController
 * @package App\Infrastructure\Symfony\Api\Company\V2\Controller
 */
class UpdateCompanyController extends BaseController
{
    /**
     * @Route(path="/", methods={"PUT"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCompanyAction(
        Request $request
    ): JsonResponse {
        $command = new UpdateCompanyCommand();
        $form = $this->createForm(UpdateCompanyType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            $this->logger->critical(
                'Invalid data',
                [
                    'data' => $form->getData(),
                    'errors' => $this->getValidationErrors($form),
                    'method' => __METHOD__,
                ]
            );

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
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__
                ]
            );

            if ($exception->getCode() === Response::HTTP_NOT_FOUND) {
                return $this->createApiResponse(
                    [
                        'success' => false,
                        'error' => 'An internal server error has been occurred. ' . $exception->getMessage(),
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => $exception->getMessage(),
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
