<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Application\User\Command\LoginUssdCommand;
use App\Application\User\CommandHandler\LoginUssdHandler;
use App\Controller\BaseController;
use App\Controller\User\FormType\LoginUssdType;
use App\Entity\Contracts\LoginUssdRequest;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LoginUssdController
 * @package App\Controller\User
 */
class LoginUssdController extends BaseController
{
    /**
     * @param Request $request
     * @param LoggerInterface $logger
     * @param LoginUssdHandler $handler
     * @return JsonResponse
     */
    public function action(
        Request $request,
        LoggerInterface $logger,
        LoginUssdHandler $handler
    ): JsonResponse {
        $contract = new LoginUssdRequest();
        $form = $this->createForm(LoginUssdType::class, $contract);
        $this->processForm($request, $form) ;

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                [
                    'errors' => $this->getValidationErrors($form),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command = new LoginUssdCommand(
            $contract->getTin(),
            $contract->getPin()
        );

        try {
            $handler->__invoke($command);
        } catch (Exception $exception) {
            $logger->critical(
                'Error trying to login with ussd',
                [
                    'code' => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error_message' => 'Error trying to login with ussd. ' . $exception->getMessage(),
                ],
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
