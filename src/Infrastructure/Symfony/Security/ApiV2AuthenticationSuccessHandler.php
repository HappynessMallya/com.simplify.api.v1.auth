<?php

namespace App\Infrastructure\Symfony\Security;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class ApiV2AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /** @var JWTTokenManagerInterface  */
    private JWTTokenManagerInterface $JWTTokenManager;

    /** @var CompanyByUserRepository  */
    private CompanyByUserRepository $companyByUserRepository;

    /** @var CompanyRepository  */
    private CompanyRepository $companyRepository;

    /** @var UserRepository  */
    private UserRepository $userRepository;

    /** @var RefreshTokenManagerInterface  */
    private RefreshTokenManagerInterface $refreshTokenManager;

    /**
     * @param JWTTokenManagerInterface $JWTTokenManager
     * @param CompanyByUserRepository $companyByUserRepository
     * @param CompanyRepository $companyRepository
     * @param UserRepository $userRepository
     * @param RefreshTokenManagerInterface $refreshTokenManager
     */
    public function __construct(
        JWTTokenManagerInterface $JWTTokenManager,
        CompanyByUserRepository $companyByUserRepository,
        CompanyRepository $companyRepository,
        UserRepository $userRepository,
        RefreshTokenManagerInterface $refreshTokenManager
    ) {
        $this->JWTTokenManager = $JWTTokenManager;
        $this->companyByUserRepository = $companyByUserRepository;
        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return JsonResponse
     * @throws \Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $userEntity = $token->getUser();
        $userId = $token->getUser()->userId();

        $user = $this->userRepository->get($userId);

        $companies = $this->companyByUserRepository->getCompaniesByUser($userId);

        $companiesUser = [];
        foreach ($companies as $companyUser) {
            $company = $this->companyRepository->get(CompanyId::fromString($companyUser['company_id']));

            $companiesUser[] = [
                'company_id' => $company->companyId()->toString(),
                'name' => $company->name(),
                'vrn' => !(($company->traRegistration()['VRN'] == 'NOT REGISTERED')),
            ];
        }

        $payload = [
            'userId' => $userId->toString(),
            'username' => $user->username(),
            'companies' => $companiesUser,
            'userType' => $user->getUserType()->toString(),
            'lastLogin' => (!empty($user->lastLogin())) ? $user->lastLogin()->setTimezone(new DateTimeZone('Africa/Dar_es_Salaam'))
                ->format('Y-m-d H:i:s') : null,
            'status' => $user->status()->toString(),
        ];

        $token = $this->JWTTokenManager->createFromPayload($userEntity, $payload);

        $this->userRepository->login($userId);

        $refreshToken = new RefreshToken();
        $refreshToken->setUsername($user->email());
        $refreshToken->setRefreshToken(bin2hex(random_bytes(64)));

        $datetime = new DateTime();
        $datetime->setTimestamp(time() + $_ENV['JWT_REFRESH_TOKEN_TIME']);

        $refreshToken->setValid($datetime);
        $this->refreshTokenManager->save($refreshToken);

        return new JsonResponse(
            [
                'token' => $token,
                'refresh_token' => $refreshToken->getRefreshToken(),
            ],
            Response::HTTP_OK
        );
    }
}