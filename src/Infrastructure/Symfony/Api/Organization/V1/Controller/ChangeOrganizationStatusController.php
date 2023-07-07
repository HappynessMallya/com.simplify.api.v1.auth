<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\V1\Controller;

use App\Application\Organization\V1\Command\ChangeOrganizationStatusCommand;
use App\Application\Organization\V1\CommandHandler\ChangeOrganizationStatusHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\Organization\V1\FormType\ChangeOrganizationStatusType;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ChangeOrganizationStatusController
 * @package App\Infrastructure\Symfony\Api\Organization\V1\Controller
 */
class ChangeOrganizationStatusController extends BaseController
{
    /**
     * @Route(path="/changeStatus", methods={"PUT"})
     *
     * @param Request $request
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param ChangeOrganizationStatusHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function changeOrganizationStatusAction(
        Request $request,
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        ChangeOrganizationStatusHandler $handler
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $userTypeWhoChangeStatus = $tokenData['userType'];

        $command = new ChangeOrganizationStatusCommand();
        $form = $this->createForm(ChangeOrganizationStatusType::class, $command);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            $this->logger->critical(
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

        $command->setUserType($userTypeWhoChangeStatus);

        try {
            $isStatusChanged = $handler->__invoke($command);
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

        if (!$isStatusChanged) {
            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Organization status has not been changed',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->createApiResponse(
            [
                'success' => true,
                'message' => 'Organization status changed successfully',
            ],
            Response::HTTP_OK
        );
    }
}
