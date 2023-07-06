<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V2\Controller;

use App\Application\User\V2\Command\ChangeUserStatusCommand;
use App\Application\User\V2\CommandHandler\ChangeUserStatusHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\V2\FormType\ChangeUserStatusType;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ChangeUserStatusController
 * @package App\Infrastructure\Symfony\Api\User\V2\Controller
 */
class ChangeUserStatusController extends BaseController
{
    /**
     * @Route(path="/operator/changeStatus", methods={"PUT"})
     *
     * @param Request $request
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param ChangeUserStatusHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function changeUserStatusAction(
        Request $request,
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        ChangeUserStatusHandler $handler
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $userTypeWhoChangeStatus = $tokenData['userType'];

        $command = new ChangeUserStatusCommand();
        $form = $this->createForm(ChangeUserStatusType::class, $command);
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

        $command->setUserType($userTypeWhoChangeStatus);

        try {
            $isStatusChanged = $handler->__invoke($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to change user status',
                [
                    'error_message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Exception error trying to change user status. ' . $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$isStatusChanged) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'User status has not changed',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'message' => 'User status changed',
            ],
            Response::HTTP_OK
        );
    }
}
