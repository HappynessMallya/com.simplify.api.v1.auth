<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\Controller;


use App\Application\Organization\QueryHandler\GetCompaniesByParamsHandler;
use App\Application\Organization\QueryHandler\GetCompaniesByParamsQuery;
use App\Infrastructure\Symfony\Api\BaseController;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GetCompaniesByParamsController
 * @package App\Infrastructure\Symfony\Api\User\Controller\V2
 */
class GetCompaniesByParamsController extends BaseController
{
    /**
     * @Route(path="/companies/query", methods={"GET"})
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param TokenStorageInterface $jwtStorage
     * @param Request $request
     * @param LoggerInterface $logger
     * @param GetCompaniesByParamsHandler $handler
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getCompaniesByParamsAction(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $jwtStorage,
        Request $request,
        LoggerInterface $logger,
        GetCompaniesByParamsHandler $handler
    ): JsonResponse {
        $tokenData = $jwtManager->decode($jwtStorage->getToken());
        $organizationId = $tokenData['organizationId'];
        $userId = $tokenData['userId'];
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
            $userId,
            $userType,
            $companyName ?? '',
            $tin ?? '',
            $vrn ?? '',
            $email ?? '',
            $mobileNumber ?? '',
            $serial ?? '',
            $status,
        );

        try {
            $companies = $handler->__invoke($query);

            if (!$companies) {
                $this->logger->debug(
                    'No companies found by the search criteria',
                    [
                        'criteria' => [
                            'userId' => $userId,
                            'userType' => $userType,
                            'companyName' => $companyName,
                            'tin' => $tin,
                            'vrn' => $vrn,
                            'email' => $email,
                            'mobileNumber' => $mobileNumber,
                            'serial' => $serial,
                            'status' => $status,
                        ],
                    ]
                );

                return $this->createApiResponse(
                    [
                        'success' => false,
                        'error' => 'No companies found by the search criteria',
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        } catch (Exception $exception) {
            $logger->critical(
                'Error trying to find the set of companies',
                [
                    'user_id' => $userId,
                    'user_type' => $userType,
                    'error_message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return $this->createApiResponse(
                [
                    'success' => false,
                    'error' => 'Error trying to find the set of companies. ' . $exception->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->createApiResponse(
            $companies,
            Response::HTTP_OK
        );
    }
}
