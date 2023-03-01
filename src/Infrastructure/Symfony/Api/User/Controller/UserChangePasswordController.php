<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller;

use App\Application\User\Command\UserChangePasswordCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\Form\UserChangePasswordType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserChangePasswordController
 * @package App\Infrastructure\Symfony\Api\User\Controller
 */
class UserChangePasswordController extends BaseController
{
    /**
     * @Route(path="/change-password", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userChangePasswordAction(Request $request): JsonResponse
    {
        $command = new UserChangePasswordCommand();
        $form = $this->createForm(UserChangePasswordType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                [
                    'errors' => $this->getValidationErrors($form),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $changed = false;

        try {
            $changed = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to change password',
                [
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            if ($exception->getCode() === Response::HTTP_NOT_FOUND) {
                return $this->createApiResponse(
                    [
                        'errors' => $exception->getMessage(),
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        }

        return $this->createApiResponse(
            [
                'success' => $changed,
            ],
            $changed ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }
}
