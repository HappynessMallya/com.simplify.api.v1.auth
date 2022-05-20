<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller;

use App\Application\Company\Command\ChangeStatusCompanyCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\Form\ChangeStatusCompanyType;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangeStatusCompanyController extends BaseController
{
    /**
     * @Route(path="/changeStatus/{tinId}", methods={"PUT"})
     *
     * @param Request $request
     * @param LoggerInterface $logger
     * @param string $tinId
     * @return JsonResponse
     */
    public function changeStatusCompanyAction(
        Request $request,
        LoggerInterface $logger,
        string $tinId
    ): JsonResponse {
        $command = new ChangeStatusCompanyCommand();
        $form = $this->createForm(ChangeStatusCompanyType::class, $command);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            return $this->createApiResponse(
                [
                    'errors' => $this->getValidationErrors($form),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command->setTin($tinId);
        try {
            $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $logger->critical(
                'Unexpected error',
                [
                    'error' => $exception->getMessage(),
                ]
            );
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
