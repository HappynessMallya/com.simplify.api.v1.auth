<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\V1\Controller;

use App\Application\Organization\V1\Command\CreateOrganizationCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Organization\V1\FormType\CreateOrganizationType;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreateOrganizationController
 * @package App\Infrastructure\Symfony\Api\Organization\V1\Controller
 */
class CreateOrganizationController extends BaseController
{
    /**
     * @Route(path="/", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function createOrganizationAction(
        Request $request
    ): JsonResponse {
        $command = new CreateOrganizationCommand();
        $form = $this->createForm(CreateOrganizationType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            $this->logger->critical(
                'Invalid data',
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

        try {
            $organizationId = $this->commandBus->handle($command);
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
