<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Services\SendCredentialsRequest;
use App\Domain\Services\SendCredentialsInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SendCredentialsServiceTest extends KernelTestCase
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
                        'http_code' => 200,
                    ]
                );
            }
        );

        $kernel->getContainer()->set(HttpClientInterface::class, $mockHttpClient);

        /** @var $sendCredentialsService */
        $sendCredentialsService = $kernel->getContainer()->get(SendCredentialsInterface::class);

        $request = new SendCredentialsRequest(
            'NEW_CREDENTIALS',
            'john.doe@example.co.tz',
            'john.doe@example.co.tz',
            'Simplify'
        );

        $response = $sendCredentialsService->onSendCredentials($request);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals($expectedResponse, $response->getErrorMessage());
    }
}
