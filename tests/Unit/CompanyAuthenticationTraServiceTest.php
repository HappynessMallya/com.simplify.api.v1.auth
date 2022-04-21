<?php

namespace App\Tests;

use App\Domain\Model\Company\CompanyId;
use App\Domain\Services\CompanyAuthenticationTraRequest;
use App\Domain\Services\CompanyAuthenticationTraService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CompanyAuthenticationTraServiceTest extends KernelTestCase
{
    public function testRequestTokenTraWhenIsSuccess(): void
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

        /** @var $companyAuthenticationTraService CompanyAuthenticationTraService */
        $companyAuthenticationTraService = $kernel->getContainer()->get(CompanyAuthenticationTraService::class);

        $request = new CompanyAuthenticationTraRequest(
            CompanyId::generate()->toString(),
            '123456789'
        );

        $response = $companyAuthenticationTraService->requestTokenAuthenticationTra($request);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals($expectedResponse, $response->getErrorMessage());
    }

    public function testRequestTokenTraWhenTinIsEmptyError(): void
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

        /** @var $companyAuthenticationTraService CompanyAuthenticationTraService */
        $companyAuthenticationTraService = $kernel->getContainer()->get(CompanyAuthenticationTraService::class);

        $request = new CompanyAuthenticationTraRequest(
            CompanyId::generate()->toString(),
            ''
        );

        $response = $companyAuthenticationTraService->requestTokenAuthenticationTra($request);

        $this->assertFalse($response->isSuccess());
        $this->assertEquals($expectedResponse, $response->getErrorMessage());
    }

    public function testRequestTokenTraWhenCompanyIdIsEmptyError(): void
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

        /** @var $companyAuthenticationTraService CompanyAuthenticationTraService */
        $companyAuthenticationTraService = $kernel->getContainer()->get(CompanyAuthenticationTraService::class);

        $request = new CompanyAuthenticationTraRequest(
            '',
            '123456789'
        );

        $response = $companyAuthenticationTraService->requestTokenAuthenticationTra($request);

        $this->assertFalse($response->isSuccess());
        $this->assertEquals($expectedResponse, $response->getErrorMessage());
    }

    public function testRequestTokenTraWhenThrowErrors(): void
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

        /** @var $companyAuthenticationTraService CompanyAuthenticationTraService */
        $companyAuthenticationTraService = $kernel->getContainer()->get(CompanyAuthenticationTraService::class);

        $request = new CompanyAuthenticationTraRequest(
            CompanyId::generate()->toString(),
            '123456789'
        );

        $response = $companyAuthenticationTraService->requestTokenAuthenticationTra($request);

        $this->assertFalse($response->isSuccess());
    }
}
