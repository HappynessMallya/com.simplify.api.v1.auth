<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Services;

use App\Domain\Services\CompanyAuthenticationTraRequest;
use App\Domain\Services\CompanyAuthenticationTraResponse;
use App\Domain\Services\CompanyAuthenticationTraService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CompanyAuthenticationTraClient implements CompanyAuthenticationTraService
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
     * @param CompanyAuthenticationTraRequest $request
     * @return CompanyAuthenticationTraResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function requestTokenAuthenticationTra(
        CompanyAuthenticationTraRequest $request
    ): CompanyAuthenticationTraResponse {
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
                    ]
                );
            }

            $response->getContent();

            return new CompanyAuthenticationTraResponse(
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

            return new CompanyAuthenticationTraResponse(
                false,
                $exception->getResponse()->getContent(false)
            );
        }
    }
}
