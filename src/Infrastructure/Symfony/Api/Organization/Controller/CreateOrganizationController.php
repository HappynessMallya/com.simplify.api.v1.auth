<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\Controller;

use App\Application\Organization\CommandHandler\CreateOrganizationCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Organization\Controller\FormType\CreateOrganizationType;
use DateTime;
use DateTimeZone;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreateOrganizationController
 * @package App\Infrastructure\Symfony\Api\Organization\Controller
 */
class CreateOrganizationController extends BaseController
{
    /**
     * @Route(path="/", methods={"POST"})
     *
     * @param Request $request
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @throws Exception
     */
    public function createOrganizationAction(
        Request $request,
        LoggerInterface $logger
    ): JsonResponse {
        try {
            $command = new CreateOrganizationCommand();
            $form = $this->createForm(CreateOrganizationType::class, $command);
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

            $organizationId = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal error has been occurred',
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'errors' => 'Organization has not created: ' . $exception->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'organizationId' => $organizationId,
                'createdAt' => (
                    new DateTime('now', new DateTimeZone('Africa/Dar_es_Salaam'))
                )->format('Y-m-d H:i:s'),
            ],
            Response::HTTP_CREATED
        );
    }
}
