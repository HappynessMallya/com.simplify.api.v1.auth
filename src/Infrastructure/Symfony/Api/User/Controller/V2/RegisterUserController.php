<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller\V2;

use App\Application\User\V2\CommandHandler\RegisterUserCommand;
use App\Application\User\V2\CommandHandler\RegisterUserHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\Controller\V2\FormType\RegisterUserType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegisterUserController
 * @package App\Infrastructure\Symfony\Api\ApiUser\Controller\V2
 */
class RegisterUserController extends BaseController
{
    /**
     * @Route(path="/register", methods={"POST"})
     *
     * @param Request $request
     * @param RegisterUserHandler $handler
     * @return JsonResponse
     */
    public function registerUserAction(
        Request $request,
        RegisterUserHandler $handler
    ): JsonResponse {
        $this->logger->debug(
            'Register new user',
            [
                'data' => json_decode($request->getContent(), true),
            ]
        );

        $command = new RegisterUserCommand();
        $form = $this->createForm(RegisterUserType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            $this->logger->critical(
                'The data could not be validated',
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
            $response = $handler->__invoke($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to register user',
                [
                    'error_message' => $exception->getMessage(),
                    'error_code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error_message' => 'Exception error trying to register user. ' . $exception->getMessage(),
                ],
                $exception->getCode()
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'userId' => $response['userId'],
                'username' => $response['username'],
                'createdAt' => $response['createdAt']
            ],
            Response::HTTP_CREATED
        );
    }
}
