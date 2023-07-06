<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\Controller;

use App\Application\Company\V1\Command\CompanyTraRegistrationCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\V1\FormType\TraRegistrationCompanyType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompanyTraRegistrationController
 * @package App\Infrastructure\Symfony\Api\Company\V1\Controller
 */
class CompanyTraRegistrationController extends BaseController
{
    /**
     * @Route(path="/{tin}", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function companyTraRegistrationAction(
        Request $request
    ): JsonResponse {
        $updated = false;
        $tin = $request->get('tin');

        $command = new CompanyTraRegistrationCommand();
        $form = $this->createForm(TraRegistrationCompanyType::class, $command);
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

        $command->setTin($tin);

        try {
            $updated = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal server error has been occurred',
                [
                    'code' => $exception->getCode(),
                    'error' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            if ($exception->getCode() === Response::HTTP_NOT_FOUND) {
                return $this->createApiResponse(
                    [
                        'success' => false,
                        'error' => 'An internal server error has been occurred. ' . $exception->getMessage(),
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

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
                'success' => $updated,
                'message' => 'Company TRA registration successful',
            ],
            Response::HTTP_OK
        );
    }
}
