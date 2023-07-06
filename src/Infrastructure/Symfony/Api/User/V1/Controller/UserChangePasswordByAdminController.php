<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V1\Controller;

use App\Application\User\Command\UserChangePasswordByAdminCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\V1\Form\UserChangePasswordByAdminType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserChangePasswordByAdminController
 * @package App\Infrastructure\Symfony\Api\User\Controller
 */
class UserChangePasswordByAdminController extends BaseController
{
    /**
     * @Route(path="/change-password", methods={"PUT"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userChangePasswordByAdminAction(Request $request): JsonResponse
    {
        $command = new UserChangePasswordByAdminCommand();
        $form = $this->createForm(UserChangePasswordByAdminType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                [
                    'errors' => $this->getValidationErrors($form),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $changed = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to change password',
                [
                    'error_message' => $exception->getMessage(),
                    'error_code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Exception error trying to change password. ' . $exception->getMessage(),
                ],
                $exception->getCode()
            );
        }

        if (!$changed) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Invalid current password',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'message' => 'Password changed successfully',
            ],
            Response::HTTP_OK
        );
    }
}
