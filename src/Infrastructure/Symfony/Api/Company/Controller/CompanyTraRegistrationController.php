<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\Controller;

use App\Application\Company\Command\CompanyTraRegistrationCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Company\Form\TraRegistrationCompanyType;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompanyTraRegistrationController
 * @package App\Infrastructure\Symfony\Api\Company\Controller
 */
class CompanyTraRegistrationController extends BaseController
{
    /**
     * @Route(path="/{tin}", methods={"POST"})
     *
     * @param Request $request
     * @param string $tin
     * @return JsonResponse
     */
    public function action(
        Request $request,
        string $tin
    ): JsonResponse {
        $updated = false;
        $command = new CompanyTraRegistrationCommand();
        $form = $this->createForm(TraRegistrationCompanyType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                ['errors' => $this->getValidationErrors($form)],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command->setTin($tin);

        try {
            $updated = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->logger->critical(
                $exception->getMessage(),
                [
                    'method' => __METHOD__,
                ]
            );

            if ($exception->getCode() === 404) {
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
            Response::HTTP_OK
        );
    }
}
