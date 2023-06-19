<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller\V2;

use App\Application\Company\V2\CommandHandler\RegisterCompanyCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\Controller\V2\FormType\RegisterCompanyType;
use DateTime;
use DateTimeZone;
use Exception;
use Psr\Log\LoggerInterface;
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
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @throws Exception
     */
    public function action(Request $request, LoggerInterface $logger): JsonResponse
    {
        try {
            $command = new RegisterCompanyCommand();
            $form = $this->createForm(RegisterCompanyType::class, $command);
            $this->processForm($request, $form);

            if ($form->isValid() === false) {
                $logger->critical(
                    'The form contains errors',
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
                    'errors' => 'Company has not been created',
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
