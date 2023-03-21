<?php

namespace App\Infrastructure\Symfony\Security;

use App\Application\Company\Command\RequestAuthenticationTraCommand;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class ApiV1AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var MessageBusInterface */
    private MessageBusInterface $messageBus;

    /** @var JWTTokenManagerInterface  */
    private JWTTokenManagerInterface $JWTTokenManager;

    private RefreshTokenManagerInterface $refreshTokenManager;

    /**
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param LoggerInterface $logger
     * @param MessageBusInterface $messageBus
     * @param JWTTokenManagerInterface $JWTTokenManager
     * @param RefreshTokenManagerInterface $refreshTokenManager
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        LoggerInterface $logger,
        MessageBusInterface $messageBus,
        JWTTokenManagerInterface $JWTTokenManager,
        RefreshTokenManagerInterface $refreshTokenManager
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->logger = $logger;
        $this->messageBus = $messageBus;
        $this->JWTTokenManager = $JWTTokenManager;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $jwtUser = $token->getUser();

        $company = $this->companyRepository->get($jwtUser->companyId());

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
                    'error' => 'Company has not been registered in TRA'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command = new RequestAuthenticationTraCommand(
            $company->companyId()->toString(),
            (string) $company->tin(),
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
                    'error' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );
        }

        $user = $this->userRepository->get($jwtUser->userId());

        $payload = [
            'lastLogin' => (!empty($user->lastLogin())) ? $user->lastLogin()->setTimezone(new DateTimeZone('Africa/Dar_es_Salaam'))
                ->format('Y-m-d H:i:s') : null,
            'status' => $user->status()->toString(),
        ];

        if (!empty($company) && !empty($company->traRegistration())) {
            $payload['companyName'] = $company->name();
            $payload['vrn'] = $company->traRegistration()['VRN'] !== 'NOT REGISTERED';
        }

        $this->userRepository->login($user->userId());

        $token = $this->JWTTokenManager->createFromPayload($jwtUser, $payload);

        $refreshToken = $this->refreshTokenManager->create();
        $refreshToken->setRefreshToken(bin2hex(random_bytes(64)));
        $refreshToken->setUsername($user->email());
        $refreshToken->setValid((new DateTime())->setTimestamp(time() + $_ENV['JWT_REFRESH_TOKEN_TIME']));

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