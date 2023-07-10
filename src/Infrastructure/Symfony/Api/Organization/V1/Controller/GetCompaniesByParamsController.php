<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\V1\Controller;

use App\Application\Organization\V1\Query\GetCompaniesByParamsQuery;
use App\Application\Organization\V1\QueryHandler\GetCompaniesByParamsHandler;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GetCompaniesByParamsController
 * @package App\Infrastructure\Symfony\Api\Organization\V1\Controller
 */
class GetCompaniesByParamsController extends BaseController
{
    /**
     * @Route(path="/companies/getBy/query", methods={"GET"})
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param Request $request
     * @param GetCompaniesByParamsHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getCompaniesByParamsAction(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        Request $request,
        GetCompaniesByParamsHandler $handler
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $organizationId = $tokenData['organizationId'];
        $userType = $tokenData['userType'];

        $companyName = $request->query->get('companyName');
        $tin = $request->query->get('tin');
        $vrn = $request->query->get('vrn');
        $email = $request->query->get('email');
        $mobileNumber = $request->query->get('mobileNumber');
        $serial = $request->query->get('serial');
        $status = $request->query->get('status');

        $query = new GetCompaniesByParamsQuery(
            $organizationId,
            $userType,
            $companyName ?? '',
            $tin ?? '',
            $vrn ?? '',
            $email ?? '',
            $mobileNumber ?? '',
            $serial ?? '',
            $status ?? 'ALL',
        );

        try {
            $companies = $handler->__invoke($query);
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

        return $this->createApiResponse(
            $companies,
            Response::HTTP_OK
        );
    }
}
