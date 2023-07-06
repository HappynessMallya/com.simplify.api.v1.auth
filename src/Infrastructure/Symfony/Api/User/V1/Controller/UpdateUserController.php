<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V1\Controller;

use App\Application\User\V1\Command\UpdateUserCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\V1\FormType\UpdateUserType;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class UpdateUserController
 * @package App\Infrastructure\Symfony\Api\User\V1\Controller
 */
class UpdateUserController extends BaseController
{
    /**
     * @Route(path="/", methods={"PUT"})
     *
     * @param Request $request
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function updateUserAction(
        Request $request,
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $username = $tokenData['username'];

        $command = new UpdateUserCommand();
        $form = $this->createForm(UpdateUserType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            $this->logger->critical(
                'Invalid form',
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

        $command->setUsername($username);

        $isUpdated = false;

        try {
            $isUpdated = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to update user',
                [
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            if ($exception->getCode() === Response::HTTP_NOT_FOUND) {
                return $this->createApiResponse(
                    [
                        'success' => false,
                        'errors' => 'Exception error trying to update user. ' . $exception->getMessage(),
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        }

        if (!$isUpdated) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'message' => 'User has not been updated',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'message' => 'User updated successfully',
            ],
            Response::HTTP_OK
        );
    }
}
