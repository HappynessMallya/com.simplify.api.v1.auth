<?php

namespace App\Infrastructure\Domain\Services;

use App\Domain\Services\CreateSubscriptionRequest;
use App\Domain\Services\CreateSubscriptionResponse;
use App\Domain\Services\SubscriptionService;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SubscriptionClient implements SubscriptionService
{
    /** @var LoggerInterface  */
    private LoggerInterface $logger;

    /** @var HttpClientInterface  */
    private HttpClientInterface $httpClient;

    /** @var string  */
    private string $url;

    /**
     * @param LoggerInterface $logger
     * @param HttpClientInterface $httpClient
     */
    public function __construct(LoggerInterface $logger, HttpClientInterface $httpClient)
    {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->url = $_ENV['SUBSCRIPTION_SERVICE_URL'] . 'create';
    }

    /**
     * @param CreateSubscriptionRequest $request
     * @return CreateSubscriptionResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function createSubscription(CreateSubscriptionRequest $request): CreateSubscriptionResponse
    {
        $this->logger->info(
            'SubscriptionClient::createSubscription()',
            [
                'company_id' => $request->getCompanyId(),
            ]
        );

        $payload = [
            'companyId' => $request->getCompanyId(),
            'type' => $request->getType(),
            'date' => $request->getDate(),
        ];

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->url,
                [
                    'headers' => [
                        'application/json'
                    ],
                    'body' => json_encode($payload)
                ]
            );

            if ($response->getStatusCode() == 200) {
                $this->logger->info(
                    'Subscription has been managed successfully',
                    [
                        'companyId' => $request->getCompanyId(),
                        'payload' => $response->getContent(),
                    ]
                );
            }

            $content = $response->getContent();

            return new CreateSubscriptionResponse(
                true,
                ''
            );
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $exception) {
            $this->logger->critical(
                'An error has occurred in the process of create subscription',
                [
                    'company_id' => $request->getCompanyId(),
                    'http_status_code' => $exception->getResponse()->getStatusCode(),
                    'http_body' => $exception->getResponse()->getContent(false),
                    'error_code' => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return new CreateSubscriptionResponse(
                false,
                $exception->getResponse()->getContent(false)
            );
        } catch (TransportExceptionInterface $exception) {
            $this->logger->critical(
                'An error has occurred in the network in the create process of subscription',
                [
                    'company_id' => $request->getCompanyId(),
                    'error_code' => $exception->getCode(),
                    'error_message' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            return new CreateSubscriptionResponse(
                false,
                $exception->getMessage()
            );
        }
    }
}