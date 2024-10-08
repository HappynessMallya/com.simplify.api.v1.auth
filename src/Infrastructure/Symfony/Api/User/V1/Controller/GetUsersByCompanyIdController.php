<?php

namespace App\Infrastructure\Symfony\Api\User\V1\Controller;

use App\Application\User\V1\Query\GetUsersByCompanyIdQuery;
use App\Application\User\V1\QueryHandler\GetUsersByCompanyIdHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use App\Domain\Model\User\UserType;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\Annotation\Route;

class GetUsersByCompanyIdController extends BaseController
{
    /**
     * @Route(path="/company/{companyId}", methods={"GET"})
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param GetUsersByCompanyIdHandler $handler
     * @param string $companyId
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     * @throws \Exception
     */
    public function getUsersByCompany(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        GetUsersByCompanyIdHandler $handler,
        string $companyId
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());

        if (
            !UserType::TYPE_OWNER()->sameValueAs(UserType::byName($tokenData['userType'])) &&
            !UserType::TYPE_ADMIN()->sameValueAs(UserType::byName($tokenData['userType']))
        ) {
            return $this->json('This user does not has permission to fetch this data', 403);
        }

        $query = new GetUsersByCompanyIdQuery($companyId);

        $users = $handler->__invoke($query);

        return $this->json($users);
    }
}
