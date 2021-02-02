<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller;

use App\Application\User\Command\UserChangePasswordCommand;
use App\Domain\Model\User\UserStatus;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\Form\UserChangePasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserChangePasswordByAdminController
 * @package App\Infrastructure\Symfony\Api\User\Controller
 */
class UserChangePasswordByAdminController extends BaseController
{
    /**
     * @Route(path="/change-password", methods={"PUT"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userChangePasswordAction(Request $request)
    {
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
            $userChangePasswordCommand->setStatus(UserStatus::CHANGE_PASSWORD()->toString());
            $registered = $this->commandBus->handle($userChangePasswordCommand);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), [__METHOD__]);
        }

        return $this->createApiResponse(['success' => $registered], Response::HTTP_OK);
    }
}
