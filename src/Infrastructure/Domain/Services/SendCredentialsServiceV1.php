<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Services;

use App\Domain\Services\SendCredentialsRequest;
use App\Domain\Services\SendCredentialsResponse;
use App\Domain\Services\SendCredentialsService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class SendCredentialsServiceV1
 * @package App\Infrastructure\Domain\Services
 */
class SendCredentialsServiceV1 implements SendCredentialsService
{
    public const SEND_CREDENTIALS_ENDPOINT = 'credentials/send';

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var HttpClientInterface */
    private HttpClientInterface $httpClient;

    /** @var string */
    private string $urlClient;

    /**
     * @param LoggerInterface $logger
     * @param HttpClientInterface $httpClient
     */
    public function __construct(LoggerInterface $logger, HttpClientInterface $httpClient)
    {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->urlClient = $_ENV['NOTIFICATION_SEND_CREDENTIALS_URL'] . self::SEND_CREDENTIALS_ENDPOINT;
    }

    /**
     * @param SendCredentialsRequest $request
     * @return SendCredentialsResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function onSendCredentials(SendCredentialsRequest $request): SendCredentialsResponse
    {
        $payload = [
            'reason' => $request->getReason(),
            'username' => $request->getUsername(),
            'password' => $request->getPassword(),
            'email' => $request->getEmail(),
            'company' => $request->getCompany(),
        ];

        $this->logger->debug(
            'SendCredentialsService::onSendCredentials()',
            [
                'url' => $this->urlClient,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $payload,
            ]
        );

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->urlClient,
                [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode($payload),
                ]
            );

            if ($response->getStatusCode() === Response::HTTP_OK) {
                $this->logger->critical(
                    'Credentials sent successfully',
                    [
                        'payload' => $payload,
                        'method' => __METHOD__,
                    ]
                );
            }

            $response->getContent();

            return new SendCredentialsResponse(
                true,
                ''
            );
        } catch (
            ClientExceptionInterface
            | RedirectionExceptionInterface
            | ServerExceptionInterface $e
        ) {
            $this->logger->critical(
                $e->getMessage(),
                [
                    'payload' => $payload,
                    'response_status_code' => $e->getResponse()->getStatusCode(),
                    'response_content' => $e->getResponse()->getContent(false),
                    'code' => $e->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return new SendCredentialsResponse(
                false,
                $e->getMessage()
            );
        } catch (TransportExceptionInterface $e) {
            $this->logger->critical(
                $e->getMessage(),
                [
                    'payload' => $payload,
                    'code' => $e->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return new SendCredentialsResponse(
                false,
                $e->getMessage()
            );
        }
    }
}
