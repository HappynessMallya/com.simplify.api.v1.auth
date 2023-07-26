<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V1\Controller;

use App\Application\User\V1\Command\RegisterUserCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\V1\FormType\RegisterUserType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegisterUserController
 * @package App\Infrastructure\Symfony\Api\User\V1\Controller
 */
class RegisterUserController extends BaseController
{
    /**
     * @Route(path="/register", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerUserAction(
        Request $request
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
                'Invalid data',
                [
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
            $response = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'An internal server error has been occurred. ' . $exception->getMessage(),
                ],
                $exception->getCode()
            );
        }

        return $this->createApiResponse(
            $response,
            Response::HTTP_CREATED
        );
    }
}
