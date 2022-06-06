<?php

namespace App\Tests;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Services\CompanyStatusOnTraRequest;
use App\Domain\Services\TraIntegrationService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TraIntegrationServiceTest extends KernelTestCase
{
    public function testRequestCompanyStatusToTraWithSuccess(): void
    {
        $kernel = self::bootKernel();

        $expectedResponse = '';

        $mockHttpClient = new MockHttpClient(
            function ($method, $url, $options) use ($expectedResponse) {
                return new MockResponse(
                    $expectedResponse,
                    [
                        'http_code' => 204,
                    ]
                );
            }
        );

        $kernel->getContainer()->set(HttpClientInterface::class, $mockHttpClient);

        /** @var $traIntegrationService TraIntegrationService */
        $traIntegrationService = $kernel->getContainer()->get(TraIntegrationService::class);

        $request = new CompanyStatusOnTraRequest(
            CompanyId::generate()->toString(),
            '123456789'
        );

        $response = $traIntegrationService->requestCompanyStatusOnTra($request);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals($expectedResponse, $response->getErrorMessage());
    }

    public function testRequestCompanyStatusToTraWhenTinIsEmptyError(): void
    {
        $kernel = self::bootKernel();

        $expectedResponse = json_encode([
            'errors' => [
                'tin' => 'This value should not be blank.'
            ]
        ]);

        $mockHttpClient = new MockHttpClient(
            function ($method, $url, $options) use ($expectedResponse) {
                return new MockResponse(
                    $expectedResponse,
                    [
                        'http_code' => 400,
                    ]
                );
            }
        );

        $kernel->getContainer()->set(HttpClientInterface::class, $mockHttpClient);

        /** @var $traIntegrationService TraIntegrationService */
        $traIntegrationService = $kernel->getContainer()->get(TraIntegrationService::class);

        $request = new CompanyStatusOnTraRequest(
            CompanyId::generate()->toString(),
            ''
        );

        $response = $traIntegrationService->requestCompanyStatusOnTra($request);

        $this->assertFalse($response->isSuccess());
        $this->assertEquals($expectedResponse, $response->getErrorMessage());
    }

    public function testRequestCompanyStatusToTraWhenCompanyIdIsEmptyError(): void
    {
        $kernel = self::bootKernel();

        $expectedResponse = json_encode([
            'errors' => [
                'companyId' => 'This value should not be blank.'
            ]
        ]);

        $mockHttpClient = new MockHttpClient(
            function ($method, $url, $options) use ($expectedResponse) {
                return new MockResponse(
                    $expectedResponse,
                    [
                        'http_code' => 400,
                    ]
                );
            }
        );

        $kernel->getContainer()->set(HttpClientInterface::class, $mockHttpClient);

        /** @var $traIntegrationService TraIntegrationService */
        $traIntegrationService = $kernel->getContainer()->get(TraIntegrationService::class);

        $request = new CompanyStatusOnTraRequest(
            '',
            '123456789'
        );

        $response = $traIntegrationService->requestCompanyStatusOnTra($request);

        $this->assertFalse($response->isSuccess());
        $this->assertEquals($expectedResponse, $response->getErrorMessage());
    }

    public function testRequestCompanyStatusToTraWhenThrowErrors(): void
    {
        $kernel = self::bootKernel();

        $expectedResponse = '';

        $mockHttpClient = new MockHttpClient(
            function ($method, $url, $options) use ($expectedResponse) {
                return new MockResponse(
                    $expectedResponse,
                    [
                        'http_code' => 500,
                    ]
                );
            }
        );

        $kernel->getContainer()->set(HttpClientInterface::class, $mockHttpClient);

        /** @var $traIntegrationService TraIntegrationService */
        $traIntegrationService = $kernel->getContainer()->get(TraIntegrationService::class);

        $request = new CompanyStatusOnTraRequest(
            CompanyId::generate()->toString(),
            '123456789'
        );

        $response = $traIntegrationService->requestCompanyStatusOnTra($request);

        $this->assertFalse($response->isSuccess());
    }
}
