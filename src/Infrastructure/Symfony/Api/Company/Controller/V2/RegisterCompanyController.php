<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller\V2;

use App\Application\Company\V2\CommandHandler\RegisterCompanyCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\Controller\V2\FormType\RegisterCompanyType;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreateCompanyController
 * @package App\Infrastructure\Symfony\Api\Company\Controller\V2
 */
class RegisterCompanyController extends BaseController
{
    /**
     * @Route(path="/", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function registerCompanyAction(Request $request): JsonResponse
    {
        $command = new RegisterCompanyCommand();
        $form = $this->createForm(RegisterCompanyType::class, $command);
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
            $companyId = $this->commandBus->handle($command);
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
                    'error' => 'An internal error has been occurred. ' . $exception->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'company_id' => $companyId,
                'created_at' => (
                    new DateTime('now', new DateTimeZone('Africa/Dar_es_Salaam'))
                )->format('Y-m-d H:i:s')
            ],
            Response::HTTP_CREATED
        );
    }
}
