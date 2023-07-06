<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V2\Controller;

use App\Application\User\V2\CommandHandler\RegisterUserCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\V2\FormType\RegisterUserType;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class RegisterUserController
 * @package App\Infrastructure\Symfony\Api\ApiUser\Controller\V2
 */
class RegisterUserController extends BaseController
{
    /**
     * @Route(path="/", methods={"POST"})
     *
     * @param Request $request
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function registerUserAction(
        Request $request,
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage
    ): JsonResponse {
        $this->logger->debug(
            'Register new user',
            [
                'data' => json_decode($request->getContent(), true),
            ]
        );

        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $userIdWhoRegister = $tokenData['userId'];
        $userTypeWhoRegister = $tokenData['userType'];

        $command = new RegisterUserCommand();
        $form = $this->createForm(RegisterUserType::class, $command);
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

        $command->setUserIdWhoRegister($userIdWhoRegister);
        $command->setUserTypeWhoRegister($userTypeWhoRegister);

        try {
            $response = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to register new user',
                [
                    'error_message' => $exception->getMessage(),
                    'error_code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error_message' => 'Exception error trying to register new user. ' . $exception->getMessage(),
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
