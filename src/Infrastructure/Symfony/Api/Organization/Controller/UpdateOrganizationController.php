<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\Controller;

use App\Application\Organization\CommandHandler\UpdateOrganizationCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Organization\Controller\FormType\UpdateOrganizationType;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateOrganizationController
 * @package App\Infrastructure\Symfony\Api\Organization\Controller
 */
class UpdateOrganizationController extends BaseController
{
    /**
     * @Route(path="/update/", methods={"PUT"})
     *
     * @param Request $request
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function updateOrganizationAction(
        Request $request,
        LoggerInterface $logger
    ): JsonResponse {
        $command = new UpdateOrganizationCommand();
        $form = $this->createForm(UpdateOrganizationType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            $logger->critical(
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

        $isUpdated = false;

        try {
            $isUpdated = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Exception error trying to update organization',
                [
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            if ($exception->getCode() === Response::HTTP_NOT_FOUND) {
                return $this->createApiResponse(
                    [
                        'success' => false,
                        'errors' => 'Exception error trying to update organization. ' . $exception->getMessage(),
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        }

        if (!$isUpdated) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'message' => 'Organization has not been updated',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'message' => 'Organization updated successfully',
            ],
            Response::HTTP_OK
        );
    }
}
