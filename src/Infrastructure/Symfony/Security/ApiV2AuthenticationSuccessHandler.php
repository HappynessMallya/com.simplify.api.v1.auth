<?php

namespace App\Infrastructure\Symfony\Security;

use App\Application\Company\V1\Command\RequestAuthenticationTraCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\User\UserStatus;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\OrganizationRepository;
use App\Domain\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class ApiV2AuthenticationSuccessHandler
 * @package App\Infrastructure\Symfony\Security
 */
class ApiV2AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var JWTTokenManagerInterface */
    private JWTTokenManagerInterface $JWTTokenManager;

    /** @var CompanyByUserRepository */
    private CompanyByUserRepository $companyByUserRepository;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var RefreshTokenManagerInterface */
    private RefreshTokenManagerInterface $refreshTokenManager;

    /** @var OrganizationRepository  */
    private OrganizationRepository $organizationRepository;

    /** @var MessageBusInterface  */
    private MessageBusInterface $messageBus;

    /**
     * @param LoggerInterface $logger
     * @param JWTTokenManagerInterface $JWTTokenManager
     * @param CompanyByUserRepository $companyByUserRepository
     * @param CompanyRepository $companyRepository
     * @param UserRepository $userRepository
     * @param RefreshTokenManagerInterface $refreshTokenManager
     * @param OrganizationRepository $organizationRepository
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        LoggerInterface $logger,
        JWTTokenManagerInterface $JWTTokenManager,
        CompanyByUserRepository $companyByUserRepository,
        CompanyRepository $companyRepository,
        UserRepository $userRepository,
        RefreshTokenManagerInterface $refreshTokenManager,
        OrganizationRepository $organizationRepository,
        MessageBusInterface $messageBus
    ) {
        $this->logger = $logger;
        $this->JWTTokenManager = $JWTTokenManager;
        $this->companyByUserRepository = $companyByUserRepository;
        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->organizationRepository = $organizationRepository;
        $this->messageBus = $messageBus;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return JsonResponse
     * @throws Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $userEntity = $token->getUser();
        $userId = $token->getUser()->userId();

        $user = $this->userRepository->get($userId);

        $userCompany = $this->companyRepository->get($user->companyId());

        $companies = $this->companyByUserRepository->getCompaniesByUser($userId);

        $organization = !empty($userCompany)
            ? $this->organizationRepository->get($userCompany->organizationId())
            : null;
        $companiesUser = [];

        foreach ($companies as $companyUser) {
            $company = $this->companyRepository->get(CompanyId::fromString($companyUser['company_id']));

            if (empty($company->traRegistration())) {
                $this->logger->critical(
                    'Company has not been registered in TRA',
                    [
                        'tin' => $company->tin(),
                        'company_id' => $company->companyId(),
                        'method' => __METHOD__,
                    ]
                );

                return new JsonResponse(
                    [
                        'success' => false,
                        'error' => 'Company has not been registered in TRA',
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $companiesUser[] = [
                'company_id' => $company->companyId()->toString(),
                'name' => $company->name(),
                'vrn' => !(($company->traRegistration()['VRN'] == 'NOT REGISTERED')),
                'serial' => $company->serial(),
            ];

            $command = new RequestAuthenticationTraCommand(
                $company->companyId()->toString(),
                (string) $company->tin(),
                $company->serial(),
                $company->traRegistration()['USERNAME'],
                $company->traRegistration()['PASSWORD']
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (Exception $exception) {
                $this->logger->critical(
                    'An error has been occurred when trying request authentication in TRA',
                    [
                        'companyId' => $company->companyId()->toString(),
                        'tin' => $company->tin(),
                        'serial' => $company->serial(),
                        'error' => $exception->getMessage(),
                        'code' => $exception->getCode(),
                        'method' => __METHOD__,
                    ]
                );
            }
        }

        $payload = [
            'userId' => $userId->toString(),
            'username' => $user->username(),
            'firstName' => $user->firstName(),
            'fullName' =>  $user->firstName() . ' ' . $user->lastName(),
            'companies' => $companiesUser,
            'userType' => $user->getUserType()->toString(),
            'lastLogin' => (!empty($user->lastLogin()))
                ? $user->lastLogin()->setTimezone(new DateTimeZone('Africa/Dar_es_Salaam'))
                    ->format('Y-m-d H:i:s')
                : null,
            'status' => $user->status()->toString(),
        ];

        if (!empty($organization)) {
            $payload['organizationId'] = $organization->getOrganizationId()->toString();
            $payload['organizationName'] = $organization->getName();
        }

        $token = $this->JWTTokenManager->createFromPayload($userEntity, $payload);

        $this->userRepository->login($userId);

        $refreshToken = new RefreshToken();
        $refreshToken->setUsername($user->email());
        $refreshToken->setRefreshToken(bin2hex(random_bytes(64)));

        $datetime = new DateTime();
        $datetime->setTimestamp(time() + $_ENV['JWT_REFRESH_TOKEN_TIME']);

        $refreshToken->setValid($datetime);
        $this->refreshTokenManager->save($refreshToken);

        $response = [
            'token' => $token,
            'refresh_token' => $refreshToken->getRefreshToken(),
        ];

        if ($user->status()->sameValueAs(UserStatus::CHANGE_PASSWORD())) {
            $response['data']['change_password'] = 1;
        }

        return new JsonResponse(
            $response,
            Response::HTTP_OK
        );
    }
}
