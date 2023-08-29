<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetCompaniesByParamsControllerTest
 * @package App\Tests\Integration\Controller
 */
class GetCompaniesByParamsControllerTest extends WebTestCase
{
    public function testGetCompaniesByParamsShouldBeSuccess()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2ODc0NDQ2NTUsImV4cCI6MTY4NzQ3MzQ1NSwicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiJdLCJ1c2VySWQiOiI3ZWJlMzNjOS02Zjk4LTQxMzEtYjYyNy0wYTZkOTczMGU3YzgiLCJ1c2VybmFtZS' .
            'I6ImFuZ2xvem1AZ21haWwuY29tIiwiZmlyc3ROYW1lIjoiQW5nZWwiLCJsYXN0TmFtZSI6IkxvemFkYSIsIm9yZ2FuaXphdGlv' .
            'bklkIjoiMDAwMDMzYzktNmY5OC00MTMxLWI2MjctMGE2ZDk3MzAwMDAwIiwiY29tcGFuaWVzIjpbeyJjb21wYW55X2lkIjoiOG' .
            'RlMTEwYjQtN2VhMC00MWFjLTg5ZjQtMjliNTU5NjQyNmRiIiwibmFtZSI6IlNpbXBsaXRlY2ggTGltaXRlZCIsInZybiI6dHJ1' .
            'ZX1dLCJ1c2VyVHlwZSI6IlRZUEVfT1dORVIiLCJsYXN0TG9naW4iOiIyMDIzLTA2LTIyIDE1OjE3OjI3Iiwic3RhdHVzIjoiQU' .
            'NUSVZFIiwiZW1haWwiOiJhbmdsb3ptQGdtYWlsLmNvbSIsImNvbXBhbnlJZCI6ImRhODEyMmM2LTQ1YjEtNGUyYS05Y2EwLTk1' .
            'NmVkMjJjYmU4YyJ9.iau6wCB-UWno5UvcbNc3gN9AkUG4fNMJBV7HsrXhtoPRmzU728BveGjn95AbzX62ialAGZ7lxoEwC45Ui' .
            'KIzFBYczrf3qFCTr8R4DnbUKfo_3cgqnt4YEPs_Gko7YxXLHQ-6y1_9nCxK8SfNvHBQxo96-p-yaZoGZB_cbyMkQF-D0LZLWiu' .
            '2zvIt_vhg2AukMmwamaemgd8utIrLl_EALeK4xzHOQMSFJkhL7saa5005C7IcGowO9cIZpIi9KqOYd9zOpuEbd41Gqf9jO8T_6' .
            'RKdsqeFUz9ma45p6H-F75kljdgEvFtoCHCbvjeFXJTLlykLk4ovWFxGAp0hX5SoRQ';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        // When
        $client->request(
            'GET',
            '/api/v2/user/companies/query?status=ALL',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $response = json_decode($client->getResponse()->getContent(), true);

        // Then
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertNotNull($response);
    }

    public function testGetCompaniesByParamsWithWrongUserTypeShouldBeError()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2ODc0NjYwNzksImV4cCI6MTY4NzQ5NDg3OSwicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VySWQiOiI3ZWJlMzNjOS02Zjk4LTQxMzEtYjYyNy0wYTZkOTczMGU3ZD' .
            'AiLCJ1c2VybmFtZSI6Im1hcmlhbmFAcG8uY29tIiwiZmlyc3ROYW1lIjoiS2F0ZSIsImxhc3ROYW1lIjoiVmllbG1hIiwib3Jn' .
            'YW5pemF0aW9uSWQiOm51bGwsImNvbXBhbmllcyI6W10sInVzZXJUeXBlIjoiVFlQRV9PUEVSQVRPUiIsImxhc3RMb2dpbiI6Ij' .
            'IwMjMtMDQtMjQgMTU6NTk6MjciLCJzdGF0dXMiOiJBQ1RJVkUiLCJlbWFpbCI6Im1hcmlhbmFAcG8uY29tIiwiY29tcGFueUlk' .
            'IjoiNmFjY2I4YzMtMmZmNC00N2UzLTlkNTMtZDFlNTY2YTI4OTg5In0.P0j0BR7P3oGvKgBi1uyHFQRV8fcV_r7v8goESfA6hB' .
            'VkgRoi3z0XRgRnVy0DWUponlBlUj0aO4SfmvIa64EDM4ZQPqTu-7NUqkthONjOt4z03bMudl35TlMmY9eNgjtiY_1VkmoaWMi3' .
            'vhnntwsv5yrtsCRTcS623YR-DXR4MeS6XpDbZQfaMKC_6U4ImcJyX1S19B6Ev0P58qRYmCQHvGpKaGCXDEpA30O2C8KpJ4QsF6' .
            'xj-tQAAiIr1WqsVH12BMbCQsXHroscH5WG6DHPC-KCuNHolAWmTYQpo21Qj-zUlCY_fuE9RgX66hR6VEj-JUBU_k_ckfNhWYAg' .
            'dItozA';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        $expectedMessage = 'Error trying to find the set of companies. ' .
            'User is not an owner: OPERATOR';

        // When
        $client->request(
            'GET',
            '/api/v2/user/companies/query?status=ALL',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $response = json_decode($client->getResponse()->getContent(), true);

        // Then
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $client->getResponse()->getStatusCode());
        $this->assertFalse($response['success']);
        $this->assertEquals(
            $expectedMessage,
            $response['error']
        );
    }

    public function testGetCompaniesByParamsWithWrongParamsShouldBeEmpty()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2ODc0NDQ2NTUsImV4cCI6MTY4NzQ3MzQ1NSwicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiJdLCJ1c2VySWQiOiI3ZWJlMzNjOS02Zjk4LTQxMzEtYjYyNy0wYTZkOTczMGU3YzgiLCJ1c2VybmFtZS' .
            'I6ImFuZ2xvem1AZ21haWwuY29tIiwiZmlyc3ROYW1lIjoiQW5nZWwiLCJsYXN0TmFtZSI6IkxvemFkYSIsIm9yZ2FuaXphdGlv' .
            'bklkIjoiMDAwMDMzYzktNmY5OC00MTMxLWI2MjctMGE2ZDk3MzAwMDAwIiwiY29tcGFuaWVzIjpbeyJjb21wYW55X2lkIjoiOG' .
            'RlMTEwYjQtN2VhMC00MWFjLTg5ZjQtMjliNTU5NjQyNmRiIiwibmFtZSI6IlNpbXBsaXRlY2ggTGltaXRlZCIsInZybiI6dHJ1' .
            'ZX1dLCJ1c2VyVHlwZSI6IlRZUEVfT1dORVIiLCJsYXN0TG9naW4iOiIyMDIzLTA2LTIyIDE1OjE3OjI3Iiwic3RhdHVzIjoiQU' .
            'NUSVZFIiwiZW1haWwiOiJhbmdsb3ptQGdtYWlsLmNvbSIsImNvbXBhbnlJZCI6ImRhODEyMmM2LTQ1YjEtNGUyYS05Y2EwLTk1' .
            'NmVkMjJjYmU4YyJ9.iau6wCB-UWno5UvcbNc3gN9AkUG4fNMJBV7HsrXhtoPRmzU728BveGjn95AbzX62ialAGZ7lxoEwC45Ui' .
            'KIzFBYczrf3qFCTr8R4DnbUKfo_3cgqnt4YEPs_Gko7YxXLHQ-6y1_9nCxK8SfNvHBQxo96-p-yaZoGZB_cbyMkQF-D0LZLWiu' .
            '2zvIt_vhg2AukMmwamaemgd8utIrLl_EALeK4xzHOQMSFJkhL7saa5005C7IcGowO9cIZpIi9KqOYd9zOpuEbd41Gqf9jO8T_6' .
            'RKdsqeFUz9ma45p6H-F75kljdgEvFtoCHCbvjeFXJTLlykLk4ovWFxGAp0hX5SoRQ';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        $expectedMessage = 'No companies found by the search criteria';

        // When
        $client->request(
            'GET',
            '/api/v2/user/companies/query?status=ALL&companyName=Company Name',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $response = json_decode($client->getResponse()->getContent(), true);

        // Then
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $this->assertFalse($response['success']);
        $this->assertEquals($expectedMessage, $response['error']);
    }
}
