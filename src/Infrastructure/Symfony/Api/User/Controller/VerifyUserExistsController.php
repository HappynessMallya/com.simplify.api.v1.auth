<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller;

use App\Application\User\Command\VerifyUserExistsCommand;
use App\Application\User\CommandHandler\VerifyUserExistsHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\Form\VerifyUserExistsType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VerifyUserExistsController
 * @package App\Infrastructure\Symfony\Api\User\Controller
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
    public function userChangePasswordAction(
        Request $request,
        VerifyUserExistsHandler $handler
    ): JsonResponse {
        $verifyUserExistsCommand = new VerifyUserExistsCommand();
        $form = $this->createForm(VerifyUserExistsType::class, $verifyUserExistsCommand);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                [
                    'errors' => $this->getValidationErrors($form)
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $registered = $handler->__invoke($verifyUserExistsCommand);

            if (!$registered) {
                return $this->createApiResponse(
                    [
                        'success' => false,
                        'error_message' => 'User could not be found'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal error has been occurred',
                [
                    'code' => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error_message' => 'An internal error has been occurred. ' . $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'success' => true
            ],
            Response::HTTP_OK
        );
    }
}
