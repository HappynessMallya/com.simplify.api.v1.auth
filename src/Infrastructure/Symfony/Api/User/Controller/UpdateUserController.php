<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller;

use App\Application\User\Command\UpdateUserCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\Form\UpdateUserType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateUserController
 * @package App\Infrastructure\Symfony\Api\User\Controller
 */
class UpdateUserController extends BaseController
{
    /**
     * @Route(path="/profile/{userId}", methods={"PUT"})
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     */
    public function updateUserAction(Request $request, string $userId): JsonResponse
    {
        $command = new UpdateUserCommand();
        $form = $this->createForm(UpdateUserType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                [
                    'errors' => $this->getValidationErrors($form),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command->setUserId($userId);

        $updated = false;

        try {
            $updated = $this->commandBus->handle($command);
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
                        'errors' => $exception->getMessage(),
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        }

        return $this->createApiResponse(
            [
                'success' => $updated,
            ],
            $updated ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }
}
