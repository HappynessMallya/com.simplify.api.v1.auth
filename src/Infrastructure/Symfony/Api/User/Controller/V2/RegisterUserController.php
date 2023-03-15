<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller\V2;

use App\Application\User\V2\CommandHandler\RegisterUserCommand;
use App\Application\User\V2\CommandHandler\RegisterUserHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\Controller\V2\FormType\RegisterUserType;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function registerUserAction(
        Request $request,
        RegisterUserHandler $handler,
        LoggerInterface $logger
    ): JsonResponse {
        $logger->debug(
            'Register New User',
            [
                'data' => json_decode($request->getContent(), true)
            ]
        );

        $command = new RegisterUserCommand();
        $form = $this->createForm(RegisterUserType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            $this->logger->critical(
                'The data could not be validated',
                [
                    'errors' => $this->getValidationErrors($form)
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
            $response = $handler->__invoke($command);
        } catch (Exception $exception) {
            $logger->critical(
                'An internal error has been occurred',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]
            );

            $this->createApiResponse(
                [
                    'success' => false,
                    'error' => $exception->getMessage(),
                ]
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
