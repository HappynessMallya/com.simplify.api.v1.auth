<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller;

use App\Application\User\Command\UserChangePasswordCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\Form\UserChangePasswordType;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
    public function userChangePasswordAction(
        Request $request
    ): JsonResponse {
        $registered = false;
        $userChangePasswordCommand = new UserChangePasswordCommand();
        $form = $this->createForm(UserChangePasswordType::class, $userChangePasswordCommand);
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
            $registered = $this->commandBus->handle($userChangePasswordCommand);
        } catch (\Exception $e) {
            $this->logger->critical(
                'An error has been occurred',
                [
                    'message' => $e->getMessage(),
                    'method' => __METHOD__
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'success' => $registered
            ],
            Response::HTTP_OK
        );
    }
}
