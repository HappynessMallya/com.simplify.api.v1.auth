<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Services;

use App\Domain\Services\CompanyStatusOnTraRequest;
use App\Domain\Services\CompanyStatusOnTraResponse;
use App\Domain\Services\RegistrationCompanyToTraRequest;
use App\Domain\Services\RegistrationCompanyToTraResponse;
use App\Domain\Services\TraIntegrationService;
use App\Domain\Services\UploadCertificateToTraRegistrationRequest;
use App\Domain\Services\UploadCertificateToTraRegistrationResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class TraIntegrationClient
 * @package App\Infrastructure\Domain\Services
 */
class TraIntegrationClient implements TraIntegrationService
{
    public const REQUEST_TOKEN_ENDPOINT = 'requestToken';
    public const UPLOAD_CERTIFICATE_ENDPOINT = 'upload';
    public const REGISTRATION_COMPANY_ENDPOINT = 'register';

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var HttpClientInterface */
    private HttpClientInterface $httpClient;

    /** @var string */
    private string $urlClient;

    /**
     * TraIntegrationClient constructor
     * @param LoggerInterface $logger
     * @param HttpClientInterface $httpClient
     */
    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $httpClient
    ) {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->urlClient = $_ENV['TRA_REQUEST_TOKEN_URL'];
    }

    /**
     * @param CompanyStatusOnTraRequest $request
     * @return CompanyStatusOnTraResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function requestCompanyStatusOnTra(
        CompanyStatusOnTraRequest $request
    ): CompanyStatusOnTraResponse {
        $payload = [
            'companyId' => $request->getCompanyId(),
            'tin' => $request->getTin(),
            'serial' => $request->getSerial(),
            'username' => $request->getUsername(),
            'password' => $request->getPassword(),
        ];

        $this->logger->debug(
            'CompanyAuthenticationTraClient::requestTokenAuthenticationTra()',
            [
                'url' => $this->urlClient . self::REQUEST_TOKEN_ENDPOINT,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $payload,
                'method' => __METHOD__,
            ]
        );

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->urlClient . self::REQUEST_TOKEN_ENDPOINT,
                [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode($payload),
                ]
            );

            if ($response->getStatusCode() === Response::HTTP_NO_CONTENT) {
                $this->logger->debug(
                    'Authentication to TRA was successful',
                    [
                        'company_id' => $request->getCompanyId(),
                        'tin' => $request->getTin(),
                        'method' => __METHOD__,
                    ]
                );
            }

            $response->getContent();

            return new CompanyStatusOnTraResponse(
                true,
                ''
            );
        } catch (
            ClientExceptionInterface
            | RedirectionExceptionInterface
            | ServerExceptionInterface  $exception
        ) {
            $this->logger->critical(
                'An exception has been occurred when request token to TRA',
                [
                    'company_id' => $request->getCompanyId(),
                    'tin' => $request->getTin(),
                    'serial' => $request->getSerial(),
                    'http_status' => $exception->getResponse()->getStatusCode(),
                    'http_body' => $exception->getResponse()->getContent(false),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return new CompanyStatusOnTraResponse(
                false,
                $exception->getResponse()->getContent(false)
            );
        } catch (TransportExceptionInterface $exception) {
            $this->logger->critical(
                'An exception has been occurred when request token to TRA',
                [
                    'company_id' => $request->getCompanyId(),
                    'tin' => $request->getTin(),
                    'serial' => $request->getSerial(),
                    'code' => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return new CompanyStatusOnTraResponse(
                false,
                $exception->getMessage()
            );
        }
    }

    /**
     * @param UploadCertificateToTraRegistrationRequest $request
     * @return UploadCertificateToTraRegistrationResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function uploadCertificateToTraRegistration(
        UploadCertificateToTraRegistrationRequest $request
    ): UploadCertificateToTraRegistrationResponse {
        $formFields = [
            'tin' => $request->getTin(),
            'certificateFiles' => [
                DataPart::fromPath($request->getCertificateFiles()[0]),
                DataPart::fromPath($request->getCertificateFiles()[1]),
            ],
            'serial' => $request->getSerial(),
        ];
        $formData = new FormDataPart($formFields);

        $this->logger->debug(
            'Request for upload certificate files TraIntegrationClient::uploadCertificateToTraRegistration()',
            [
                'url' => $this->urlClient . self::UPLOAD_CERTIFICATE_ENDPOINT,
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]
        );

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->urlClient . self::UPLOAD_CERTIFICATE_ENDPOINT,
                [
                    'headers' => $formData->getPreparedHeaders()->toArray(),
                    'body' => $formData->bodyToIterable(),
                ]
            );

            if ($response->getStatusCode() == Response::HTTP_OK) {
                $this->logger->debug(
                    'Certificate uploaded successfully',
                    [
                        'tin' => $request->getTin(),
                        'certificatesFiles' => $request->getCertificateFiles(),
                    ]
                );
            }

            $response->getContent();

            return new UploadCertificateToTraRegistrationResponse(
                true,
                ''
            );
        } catch (
            ClientExceptionInterface |
            RedirectionExceptionInterface |
            ServerExceptionInterface $exception
        ) {
            $this->logger->critical(
                'An error has been occurred in client side when attempt upload certificate files',
                [
                    'tin' => $request->getTin(),
                    'http_status' => $exception->getResponse()->getStatusCode(),
                    'http_response' => $exception->getResponse()->getContent(false),
                    'errorMessage' => $exception->getMessage(),
                    'errorCode' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return new UploadCertificateToTraRegistrationResponse(
                false,
                $exception->getResponse()->getContent(false)
            );
        } catch (TransportExceptionInterface $e) {
            $this->logger->critical(
                'An error has been occurred with communication with TRA Integration API',
                [
                    'tin' => $request->getTin(),
                    'errorMessage' => $e->getMessage(),
                    'errorCode' => $e->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return new UploadCertificateToTraRegistrationResponse(
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * @param RegistrationCompanyToTraRequest $request
     * @return RegistrationCompanyToTraResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function registrationCompanyToTra(
        RegistrationCompanyToTraRequest $request
    ): RegistrationCompanyToTraResponse {
        $payload = [
            'tin' => $request->getTin(),
            'certKey' => $request->getCertificateKey(),
            'certSerial' => $request->getCertificateSerial(),
            'certPassword' => $request->getPassword()
        ];

        $this->logger->debug(
            'Request for registration company to TRA TraIntegrationService::registrationCompanyToTra()',
            [
                'url' => $this->urlClient . self::REGISTRATION_COMPANY_ENDPOINT,
                'body' => $payload
            ]
        );

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->urlClient . self::REGISTRATION_COMPANY_ENDPOINT,
                [
                    'body' => json_encode($payload)
                ]
            );

            if ($response->getStatusCode() == Response::HTTP_OK) {
                $this->logger->debug(
                    'Registration of Company successfully',
                    [
                        'tin' => $request->getTin(),
                        'serial' => $request->getCertificateKey(),
                    ]
                );
            }

            $response->getContent();

            return new RegistrationCompanyToTraResponse(
                true,
                ''
            );
        } catch (
            ClientExceptionInterface |
            RedirectionExceptionInterface |
            ServerExceptionInterface $e
        ) {
            $this->logger->critical(
                'An error has been occurred in client side attempt registration company to TRA',
                [
                    'tin' => $request->getTin(),
                    'serial' => $request->getCertificateKey(),
                    'http_status' => $e->getResponse()->getStatusCode(),
                    'http_body' => $e->getResponse()->getContent(false),
                    'code' => $e->getCode(),
                    'errorMessage' => $e->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return new RegistrationCompanyToTraResponse(
                false,
                $e->getResponse()->getContent(false)
            );
        } catch (TransportExceptionInterface $e) {
            $this->logger->critical(
                'An error has been occurred with communication with TRA Integration API',
                [
                    'tin' => $request->getTin(),
                    'serial' => $request->getCertificateKey(),
                    'errorMessage' => $e->getMessage(),
                    'errorCode' => $e->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return new RegistrationCompanyToTraResponse(
                true,
                $e->getMessage()
            );
        }
    }
}
