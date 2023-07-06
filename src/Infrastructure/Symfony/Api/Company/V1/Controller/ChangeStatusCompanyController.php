<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\Controller;

use App\Application\Company\V1\Command\ChangeStatusCompanyCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\V1\FormType\ChangeStatusCompanyType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ChangeStatusCompanyController
 * @package App\Infrastructure\Symfony\Api\Company\V1\Controller
 */
class ChangeStatusCompanyController extends BaseController
{
    /**
     * @Route(path="/changeStatus/{tin}", methods={"PUT"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changeStatusCompanyAction(
        Request $request
    ): JsonResponse {
        $tin = $request->get('tin');

        $command = new ChangeStatusCompanyCommand();
        $form = $this->createForm(ChangeStatusCompanyType::class, $command);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
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

        $command->setTin($tin);

        try {
            $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred',
                [
                    'error' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'An internal server error has been occurred. ' . $exception->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'message' => 'Company status changed successfully',
            ],
            Response::HTTP_OK
        );
    }
}
