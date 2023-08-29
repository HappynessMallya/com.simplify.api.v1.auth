<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V1\Controller;

use App\Application\User\V1\Command\VerifyUserExistsCommand;
use App\Application\User\V1\CommandHandler\VerifyUserExistsHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\V1\FormType\VerifyUserExistsType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VerifyUserExistsController
 * @package App\Infrastructure\Symfony\Api\User\V1\Controller
 */
class VerifyUserExistsController extends BaseController
{
    /**
     * @Route(path="/verify", methods={"POST"})
     *
     * @param Request $request
     * @param VerifyUserExistsHandler $handler
     * @return JsonResponse
     */
    public function verifyUserExistsAction(
        Request $request,
        VerifyUserExistsHandler $handler
    ): JsonResponse {
        $verifyUserExistsCommand = new VerifyUserExistsCommand();
        $form = $this->createForm(VerifyUserExistsType::class, $verifyUserExistsCommand);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            $this->logger->critical(
                'Invalid data',
                [
                    'errors' => $this->getValidationErrors($form),
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
            $isRegistered = $handler->__invoke($verifyUserExistsCommand);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred',
                [
                    'code' => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error_message' => 'An internal server error has been occurred. ' . $exception->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if (!$isRegistered) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'User could not be found',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'message' => 'User exists',
            ],
            Response::HTTP_OK
        );
    }
}
