<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller;

use App\Application\User\Command\RegisterUserCommand;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Infrastructure\Symfony\Api\User\Form\RegisterUserType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegisterUserController
 * @package App\Infrastructure\Symfony\Api\ApiUser\Controller
 */
class RegisterUserController extends BaseController
{
    /**
     * @Route(path="/register", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function RegisterUserAction(Request $request)
    {
        $registerUserCommand = new RegisterUserCommand();
        $form = $this->createForm(RegisterUserType::class, $registerUserCommand);
        $this->processForm($request, $form);

        if ($form->isValid() === false) {
            return $this->createApiResponse(
                ['errors' => $this->getValidationErrors($form)],
                Response::HTTP_BAD_REQUEST
            );
        }

        $registered = $this->commandBus->handle($registerUserCommand);

        return $this->createApiResponse(['success' => $registered], Response::HTTP_CREATED);
    }
}