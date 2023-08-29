<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V2\Controller;

use App\Application\User\V2\Command\ChangeEnableUserCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\V2\FormType\ChangeEnableUserType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ChangeEnableCompanyController
 * @package App\Infrastructure\Symfony\Api\User\V2\Controller
 */
class ChangeEnableUserController extends BaseController
{
    /**
     * @Route(path="/enable", methods={"PUT"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changeEnableUserAction(
        Request $request
    ): JsonResponse {
        $command = new ChangeEnableUserCommand();
        $form = $this->createForm(ChangeEnableUserType::class, $command);
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
                'message' => 'User changed successfully',
            ],
            Response::HTTP_OK
        );
    }
}
