<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller;

use App\Application\User\Query\GetUserByIdQuery;
use App\Domain\Model\User\User;
use App\Infrastructure\Symfony\Api\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetUserByIdController
 * @package App\Infrastructure\Symfony\Api\User\Controller
 */
class GetUserByIdController extends BaseController
{
    /**
     * @Route(path="/profile/{userId}", methods={"GET"})
     *
     * @param string $userId
     * @return JsonResponse
     */
    public function getUserAction(string $userId): JsonResponse
    {
        $query = new GetUserByIdQuery($userId);

        /** @var User $user */
        $user =  $this->commandBus->handle($query);

        if (empty($user)) {
            return $this->createApiResponse(
                [
                    'errors' => 'User not found by ID',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->createApiResponse(
            [
                'userId' => $user->userId()->toString(),
                'firstName' => $user->firstName(),
                'lastName' => $user->lastName(),
                'username' => $user->username(),
                'email' => $user->email(),
                'mobileNumber' => empty($user->mobileNumber()) ? '' : $user->mobileNumber(),
                'status' => $user->status()->getValue(),
                'createdAt' => $user->createdAt()->format('Y-m-d H:i:s'),
            ],
            Response::HTTP_OK
        );
    }
}
