<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\Controller;

use App\Application\Company\Command\ChangeEnableCompanyCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\V1\FormType\ChangeEnableCompanyType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ChangeEnableCompanyController
 * @package App\Infrastructure\Symfony\Api\Company\V1\Controller
 */
class ChangeEnableCompanyController extends BaseController
{
    /**
     * @Route(path="/enable", methods={"PUT"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changeEnableCompanyAction(
        Request $request
    ): JsonResponse {
        $command = new ChangeEnableCompanyCommand();
        $form = $this->createForm(ChangeEnableCompanyType::class, $command);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
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
                    'errors' => $this->getValidationErrors($form),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred',
                [
                    'code' => $exception->getCode(),
                    'error' => $exception->getMessage(),
                    'method' => __METHOD__,
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
                    'error' => 'An internal server error has been occurred. ' . $exception->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'message' => 'Enable company changed successfully',
            ],
            Response::HTTP_OK
        );
    }
}
