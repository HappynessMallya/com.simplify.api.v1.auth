<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller;

use App\Application\User\Command\UserChangePasswordCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\Form\UserChangePasswordType;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class UserChangePasswordController
 * @package App\Infrastructure\Symfony\Api\User\Controller
 */
class UserChangePasswordController extends BaseController
{
    /**
     * @Route(path="/change-password", methods={"POST"})
     *
     * @param Request $request
     * @param TokenStorageInterface $jwtStorage
     * @param JWTTokenManagerInterface $jwtManager
     * @return JsonResponse
     */
    public function userChangePasswordAction(
        Request $request,
        TokenStorageInterface $jwtStorage,
        JWTTokenManagerInterface $jwtManager
    ) {
        $registered = false;
        $userChangePasswordCommand = new UserChangePasswordCommand();
        $form = $this->createForm(UserChangePasswordType::class, $userChangePasswordCommand);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                ['errors' => $this->getValidationErrors($form)],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $tokenData = $jwtManager->decode($jwtStorage->getToken());
            if ($tokenData['username'] !== $userChangePasswordCommand->getUsername()) {
                return $this->createApiResponse(['errors' => 'Incorrect username'], Response::HTTP_BAD_REQUEST);
            }

            $registered = $this->commandBus->handle($userChangePasswordCommand);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), [__METHOD__]);
        }

        return $this->createApiResponse(['success' => $registered], Response::HTTP_OK);
    }
}
