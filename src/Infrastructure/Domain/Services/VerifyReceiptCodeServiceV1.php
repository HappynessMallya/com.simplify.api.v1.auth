<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Services;

use App\Domain\Services\VerifyReceiptCodeRequest;
use App\Domain\Services\VerifyReceiptCodeResponse;
use App\Domain\Services\VerifyReceiptCodeService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class VerifyReceiptCodeServiceV1
 * @package App\Infrastructure\Domain\Services
 */
class VerifyReceiptCodeServiceV1 implements VerifyReceiptCodeService
{
    public const VERIFY_RECEIPT_CODE_ENDPOINT = 'company/verify/receiptCode/';

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var HttpClientInterface */
    private HttpClientInterface $httpClient;

    /** @var string */
    private string $urlClient;

    /**
     * VerifyReceiptCodeServiceV1 constructor
     * @param LoggerInterface $logger
     * @param HttpClientInterface $httpClient
     */
    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $httpClient
    ) {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->urlClient = $_ENV['CORE_SERVICE_URL'] . self::VERIFY_RECEIPT_CODE_ENDPOINT;
    }

    /**
     * @param VerifyReceiptCodeRequest $request
     * @return VerifyReceiptCodeResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function onVerifyReceiptCode(
        VerifyReceiptCodeRequest $request
    ): VerifyReceiptCodeResponse {
        $this->logger->debug(
            'VerifyReceiptCodeServiceV1::onVerifyReceiptCode()',
            [
                'url' => $this->urlClient . $request->getCompanyId(),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => [
                    'receiptCode' => $request->getReceiptCode()
                ],
            ]
        );

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->urlClient . $request->getCompanyId(),
                [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode([
                        'receiptCode' => $request->getReceiptCode()
                    ]),
                ]
            );

            if ($response->getStatusCode() === Response::HTTP_OK) {
                $this->logger->debug(
                    'Receipt code verified',
                    [
                        'company_id' => $request->getCompanyId(),
                        'receipt_code' => $request->getReceiptCode(),
                        'method' => __METHOD__,
                    ]
                );
            }

            $response->getContent();

            return new VerifyReceiptCodeResponse(
                true,
                ''
            );
        } catch (
            ClientExceptionInterface
            | RedirectionExceptionInterface
            | ServerExceptionInterface $exception
        ) {
            $this->logger->critical(
                'Exception error trying to verify receipt code',
                [
                    'response_status_code' => $exception->getResponse()->getStatusCode(),
                    'response_content' => $exception->getResponse()->getContent(false),
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return new VerifyReceiptCodeResponse(
                false,
                'Exception error trying to verify receipt code: ' .
                    $exception->getResponse()->getContent(false)
            );
        } catch (TransportExceptionInterface $exception) {
            $this->logger->critical(
                'Transport exception error trying to verify receipt code',
                [
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'method' => __METHOD__,
                ]
            );

            return new VerifyReceiptCodeResponse(
                false,
                'Transport exception error trying to verify receipt code: ' . $exception->getMessage()
            );
        }
    }
}
