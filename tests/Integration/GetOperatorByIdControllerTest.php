<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetOperatorByIdControllerTest
 * @package App\Tests\Integration\Controller
 */
class GetOperatorByIdControllerTest extends WebTestCase
{
    public function testGetOperatorByIdShouldBeSuccess()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2ODEyOTcwNTEsImV4cCI6MTY4MTMyNTg1MSwicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiJdLCJ1c2VySWQiOiI3ZWJlMzNjOS02Zjk4LTQxMzEtYjYyNy0wYTZkOTczMGU3YzkiLCJ1c2VybmFtZS' .
            'I6ImRldkBkZXYuY29tIiwiZmlyc3ROYW1lIjoiTWFyaWFuYSIsImxhc3ROYW1lIjoiVmllbG1hIiwib3JnYW5pemF0aW9uSWQi' .
            'OiIwMDAwMzNjOS02Zjk4LTQxMzEtYjYyNy0wYTZkOTczMDAwMDAiLCJjb21wYW5pZXMiOlt7ImNvbXBhbnlfaWQiOiI2YWNjYj' .
            'hjMy0yZmY0LTQ3ZTMtOWQ1My1kMWU1NjZhMjg5ODgiLCJuYW1lIjoiRGV2IDEiLCJ2cm4iOnRydWV9LHsiY29tcGFueV9pZCI6' .
            'ImRhODEyMmM2LTQ1YjEtNGUyYS05Y2EwLTk1NmVkMjJjYmU4YyIsIm5hbWUiOiJTaW1wbGlmeSIsInZybiI6dHJ1ZX1dLCJ1c2' .
            'VyVHlwZSI6IlRZUEVfT1dORVIiLCJsYXN0TG9naW4iOiIyMDIzLTA0LTEyIDA3OjA0OjI0Iiwic3RhdHVzIjoiQUNUSVZFIiwi' .
            'ZW1haWwiOiJkZXZAZGV2LmNvbSIsImNvbXBhbnlJZCI6IjZhY2NiOGMzLTJmZjQtNDdlMy05ZDUzLWQxZTU2NmEyODk4OCJ9.p' .
            'Fd_PCFKJX-gq6TKzC1dXa_hgM75z9RpaQJ1lESC399OxdvRfdIVUqsKgWmW9RneXMYWc2fORPZf4JyOj-gbN4H8T7HDUE2hRq2' .
            'eFE92Z3TzxDUI-voAR055zENkcus_HU-61SFzGP3ylSKWPd1hC0l0nB0ak7GicBYJLeoZT2SyBvWHBSbhnsLRUSeIQblHE22h5' .
            'iFF0DrJt-cRVJTRvgj38git7fyi0ZkhQH2p_3XS-zoYpt2vi31dLyj8j1qp_ethQhay11EoSsQc2kZJPmzMpFPypTjUctpr4bf' .
            'oVgFGRjLQ2w0WmmPfMk7cBHNizO2dQEIofv2-ih6eevjfsA';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        $parameters = [
            'operatorId' => '7ebe33c9-6f98-4131-b627-0a6d9730e7c9',
        ];

        // When
        $client->request(
            'GET',
            '/api/v2/user/operator/' . $parameters['operatorId'],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $response = json_decode($client->getResponse()->getContent(), true);

        // Then
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertNotNull($response['operator']);
    }

    public function testGetOperatorByIdWithWrongOperatorIdShouldBeError()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2ODEyOTcwNTEsImV4cCI6MTY4MTMyNTg1MSwicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiJdLCJ1c2VySWQiOiI3ZWJlMzNjOS02Zjk4LTQxMzEtYjYyNy0wYTZkOTczMGU3YzkiLCJ1c2VybmFtZS' .
            'I6ImRldkBkZXYuY29tIiwiZmlyc3ROYW1lIjoiTWFyaWFuYSIsImxhc3ROYW1lIjoiVmllbG1hIiwib3JnYW5pemF0aW9uSWQi' .
            'OiIwMDAwMzNjOS02Zjk4LTQxMzEtYjYyNy0wYTZkOTczMDAwMDAiLCJjb21wYW5pZXMiOlt7ImNvbXBhbnlfaWQiOiI2YWNjYj' .
            'hjMy0yZmY0LTQ3ZTMtOWQ1My1kMWU1NjZhMjg5ODgiLCJuYW1lIjoiRGV2IDEiLCJ2cm4iOnRydWV9LHsiY29tcGFueV9pZCI6' .
            'ImRhODEyMmM2LTQ1YjEtNGUyYS05Y2EwLTk1NmVkMjJjYmU4YyIsIm5hbWUiOiJTaW1wbGlmeSIsInZybiI6dHJ1ZX1dLCJ1c2' .
            'VyVHlwZSI6IlRZUEVfT1dORVIiLCJsYXN0TG9naW4iOiIyMDIzLTA0LTEyIDA3OjA0OjI0Iiwic3RhdHVzIjoiQUNUSVZFIiwi' .
            'ZW1haWwiOiJkZXZAZGV2LmNvbSIsImNvbXBhbnlJZCI6IjZhY2NiOGMzLTJmZjQtNDdlMy05ZDUzLWQxZTU2NmEyODk4OCJ9.p' .
            'Fd_PCFKJX-gq6TKzC1dXa_hgM75z9RpaQJ1lESC399OxdvRfdIVUqsKgWmW9RneXMYWc2fORPZf4JyOj-gbN4H8T7HDUE2hRq2' .
            'eFE92Z3TzxDUI-voAR055zENkcus_HU-61SFzGP3ylSKWPd1hC0l0nB0ak7GicBYJLeoZT2SyBvWHBSbhnsLRUSeIQblHE22h5' .
            'iFF0DrJt-cRVJTRvgj38git7fyi0ZkhQH2p_3XS-zoYpt2vi31dLyj8j1qp_ethQhay11EoSsQc2kZJPmzMpFPypTjUctpr4bf' .
            'oVgFGRjLQ2w0WmmPfMk7cBHNizO2dQEIofv2-ih6eevjfsA';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        $parameters = [
            'operatorId' => '7ebe33c9-6f98-4131-b627-0a6d9730e7c2',
        ];

        // When
        $client->request(
            'GET',
            '/api/v2/user/operator/' . $parameters['operatorId'],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $response = json_decode($client->getResponse()->getContent(), true);

        // Then
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertFalse($response['success']);
        $this->assertEquals(
            'Exception error trying to get operator details. ' .
            'Operator not found: ' . $parameters['operatorId'],
            $response['error']
        );
    }

    public function testGetOperatorByIdWithWrongUuidShouldBeError()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2ODEyOTcwNTEsImV4cCI6MTY4MTMyNTg1MSwicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiJdLCJ1c2VySWQiOiI3ZWJlMzNjOS02Zjk4LTQxMzEtYjYyNy0wYTZkOTczMGU3YzkiLCJ1c2VybmFtZS' .
            'I6ImRldkBkZXYuY29tIiwiZmlyc3ROYW1lIjoiTWFyaWFuYSIsImxhc3ROYW1lIjoiVmllbG1hIiwib3JnYW5pemF0aW9uSWQi' .
            'OiIwMDAwMzNjOS02Zjk4LTQxMzEtYjYyNy0wYTZkOTczMDAwMDAiLCJjb21wYW5pZXMiOlt7ImNvbXBhbnlfaWQiOiI2YWNjYj' .
            'hjMy0yZmY0LTQ3ZTMtOWQ1My1kMWU1NjZhMjg5ODgiLCJuYW1lIjoiRGV2IDEiLCJ2cm4iOnRydWV9LHsiY29tcGFueV9pZCI6' .
            'ImRhODEyMmM2LTQ1YjEtNGUyYS05Y2EwLTk1NmVkMjJjYmU4YyIsIm5hbWUiOiJTaW1wbGlmeSIsInZybiI6dHJ1ZX1dLCJ1c2' .
            'VyVHlwZSI6IlRZUEVfT1dORVIiLCJsYXN0TG9naW4iOiIyMDIzLTA0LTEyIDA3OjA0OjI0Iiwic3RhdHVzIjoiQUNUSVZFIiwi' .
            'ZW1haWwiOiJkZXZAZGV2LmNvbSIsImNvbXBhbnlJZCI6IjZhY2NiOGMzLTJmZjQtNDdlMy05ZDUzLWQxZTU2NmEyODk4OCJ9.p' .
            'Fd_PCFKJX-gq6TKzC1dXa_hgM75z9RpaQJ1lESC399OxdvRfdIVUqsKgWmW9RneXMYWc2fORPZf4JyOj-gbN4H8T7HDUE2hRq2' .
            'eFE92Z3TzxDUI-voAR055zENkcus_HU-61SFzGP3ylSKWPd1hC0l0nB0ak7GicBYJLeoZT2SyBvWHBSbhnsLRUSeIQblHE22h5' .
            'iFF0DrJt-cRVJTRvgj38git7fyi0ZkhQH2p_3XS-zoYpt2vi31dLyj8j1qp_ethQhay11EoSsQc2kZJPmzMpFPypTjUctpr4bf' .
            'oVgFGRjLQ2w0WmmPfMk7cBHNizO2dQEIofv2-ih6eevjfsA';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        $parameters = [
            'operatorId' => '7ebe33c9-6f98-4131-b627',
        ];

        // When
        $client->request(
            'GET',
            '/api/v2/user/operator/' . $parameters['operatorId'],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $response = json_decode($client->getResponse()->getContent(), true);

        // Then
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertFalse($response['success']);
        $this->assertEquals(
            'Exception error trying to get operator details. ' .
            'Invalid UUID string: ' . $parameters['operatorId'],
            $response['error']
        );
    }
}
