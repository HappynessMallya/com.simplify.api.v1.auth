<?php

namespace App\Infrastructure\Symfony\Api\Company\Controller;

use App\Application\Company\Command\ChangeEnableCompanyCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\Form\ChangeEnableCompanyType;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangeEnableCompanyController extends BaseController
{
    /**
     * @Route(path="/enable", methods={"PUT"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function action(Request $request): JsonResponse
    {
        $command = new ChangeEnableCompanyCommand();
        $form = $this->createForm(ChangeEnableCompanyType::class, $command);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            return $this->createApiResponse(
                [
                    'errors' => $this->getValidationErrors($form),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'Unexpected error',
                [
                    'error' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            if ($exception->getCode() == Response::HTTP_NOT_FOUND) {
                return $this->createApiResponse(
                    $exception->getMessage(),
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->createApiResponse(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
            ],
            Response::HTTP_OK
        );
    }
}
