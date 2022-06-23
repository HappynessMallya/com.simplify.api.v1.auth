<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Services;

use App\Domain\Services\CompanyStatusOnTraRequest;
use App\Domain\Services\CompanyStatusOnTraResponse;
use App\Domain\Services\TraIntegrationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TraIntegrationClient implements TraIntegrationService
{
    public const REQUEST_TOKEN_ENDPOINT = 'requestToken';

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    /**
     * @var string
     */
    private string $urlClient;

    /**
     * @param LoggerInterface $logger
     * @param HttpClientInterface $httpClient
     */
    public function __construct(LoggerInterface $logger, HttpClientInterface $httpClient)
    {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->urlClient = $_ENV['TRA_REQUEST_TOKEN_URL'] . self::REQUEST_TOKEN_ENDPOINT;
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
        ];

        $this->logger->debug(
            'CompanyAuthenticationTraClient::requestTokenAuthenticationTra()',
            [
                'url' => $this->urlClient,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $payload,
                'time' => microtime(true)
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

            if ($response->getStatusCode() === Response::HTTP_NO_CONTENT) {
                $this->logger->debug(
                    'Authentication to TRA was successful',
                    [
                        'company_id' => $request->getCompanyId(),
                        'tin' => $request->getTin(),
                        'time' => microtime(true),
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
            | ServerExceptionInterface
            | TransportExceptionInterface $exception
        ) {
            $this->logger->critical(
                'An exception has been occurred when request token to TRA',
                [
                    'company_id' => $request->getCompanyId(),
                    'tin' => $request->getTin(),
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
        }
    }
}
