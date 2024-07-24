<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V2\Controller;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class RefreshTokenController extends AbstractController
{
    /** @var JWTTokenManagerInterface */
    private JWTTokenManagerInterface $JWTTokenManager;

    /** @var RefreshTokenManagerInterface */
    private RefreshTokenManagerInterface $refreshTokenManager;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var CompanyByUserRepository */
    private CompanyByUserRepository $companyByUserRepository;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
     * @param JWTTokenManagerInterface $jwtManager
     * @param RefreshTokenManagerInterface $refreshTokenManager
     * @param UserRepository $userRepository
     * @param CompanyByUserRepository $companyByUserRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        RefreshTokenManagerInterface $refreshTokenManager,
        UserRepository $userRepository,
        CompanyByUserRepository $companyByUserRepository,
        CompanyRepository $companyRepository
    ) {
        $this->JWTTokenManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->userRepository = $userRepository;
        $this->companyByUserRepository = $companyByUserRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(
        Request $request
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $refreshToken = $data['refresh_token'];

        if (!$refreshToken) {
            throw new AuthenticationException('Refresh token not provided');
        }

        $refreshTokenObject = $this->refreshTokenManager->get($refreshToken);

        if (!$refreshTokenObject) {
            throw new AuthenticationException('Refresh token not found');
        }

        if (!$refreshTokenObject->isValid()) {
            throw new AuthenticationException('Refresh token expired');
        }

        $user = $this->userRepository->findOneBy(['email' => $refreshTokenObject->getUsername()]);

        if (!$user) {
            throw new AuthenticationException('Refresh token is not associated with any user');
        }

        $companies = $this->companyByUserRepository->getCompaniesByUser($user->userId());

        $companiesUser = [];
        foreach ($companies as $companyUser) {
            $company = $this->companyRepository->get(CompanyId::fromString($companyUser['company_id']));

            $companiesUser[] = [
                'company_id' => $company->companyId()->toString(),
                'name' => $company->name(),
                'vrn' => !(($company->traRegistration()['VRN'] == 'NOT REGISTERED')),
                'serial' => $company->serial(),
            ];
        }

        $payload = [
            'userId' => $user->userId()->toString(),
            'username' => $user->username(),
            'companies' => $companiesUser,
            'userType' => $user->getUserType()->toString(),
        ];

        $newToken = $this->JWTTokenManager->createFromPayload($user, $payload);

        return new JsonResponse(
            [
                'token' => $newToken,
                'refresh_token' => $refreshTokenObject->getRefreshToken(),
            ],
            Response::HTTP_OK
        );
    }
}
