<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetUserByIdControllerTest
 * @package App\Tests\Integration\Controller
 */
class GetUserByIdControllerTest extends WebTestCase
{
    public function testSubmitValidDataShouldBeSuccess()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzc3NjM3NjMsImV4cCI6MTY3Nzc5MjU2Mywicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiJdLCJ1c2VybmFtZSI6ImRldkBkZXYuY29tIiwiY29tcGFueUlkIjoiNmFjY2I4YzMtMmZmNC00N2UzLT' .
            'lkNTMtZDFlNTY2YTI4OTg4IiwiY29tcGFueU5hbWUiOiJEZXYgMSIsInZybiI6dHJ1ZX0.eeI1ni-CdvNcMZ2pOjcNOy0I6tdT' .
            'f-UqUZIcgtY7izyKwBS-I5Aela8YurBkgwGQ0ryocIrPUfAde_D7brzNuxKrcZs16IXt8ZPqw-3-y-OSqlI64BJO5X_Qwf88oH' .
            'd1ZqxHx8Fy5DCdB0bg9oK8HvSPk66YCcOgw2WMZIhVew2equ7Wnj6ts_LXvmLRUYCIqXi6TZkTqE9_CTbciI-fZvWCVts1-dTM' .
            'vdBgq0ReHZFiapXQr7NiouP3XZeUeoCE0XRqitC6KFEB_mMbwWkm4wVuKghemEjPfl9Sw5t2xdCVx9pzGIHDXRItm1AsFJdfXe' .
            '9NzxxW791YcmQ8il9BEg';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        $params = [
            'userId' => '6b16ccdf-acb7-4b40-967b-f8cae44008c5',
        ];

        // When
        $client->request(
            'GET',
            '/api/v1/user/profile/' . $params['userId'],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        // Then
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent(), true)['userId']);
    }

    public function testGetUserByIdWhenUserIdIsInvalidShouldBeError()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzc3NjM3NjMsImV4cCI6MTY3Nzc5MjU2Mywicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiJdLCJ1c2VybmFtZSI6ImRldkBkZXYuY29tIiwiY29tcGFueUlkIjoiNmFjY2I4YzMtMmZmNC00N2UzLT' .
            'lkNTMtZDFlNTY2YTI4OTg4IiwiY29tcGFueU5hbWUiOiJEZXYgMSIsInZybiI6dHJ1ZX0.eeI1ni-CdvNcMZ2pOjcNOy0I6tdT' .
            'f-UqUZIcgtY7izyKwBS-I5Aela8YurBkgwGQ0ryocIrPUfAde_D7brzNuxKrcZs16IXt8ZPqw-3-y-OSqlI64BJO5X_Qwf88oH' .
            'd1ZqxHx8Fy5DCdB0bg9oK8HvSPk66YCcOgw2WMZIhVew2equ7Wnj6ts_LXvmLRUYCIqXi6TZkTqE9_CTbciI-fZvWCVts1-dTM' .
            'vdBgq0ReHZFiapXQr7NiouP3XZeUeoCE0XRqitC6KFEB_mMbwWkm4wVuKghemEjPfl9Sw5t2xdCVx9pzGIHDXRItm1AsFJdfXe' .
            '9NzxxW791YcmQ8il9BEg';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        $params = [
            'userId' => '6b16ccdf-acb7-4b40-967b-f8cae44008c',
        ];

        // When
        $client->request(
            'GET',
            '/api/v1/user/profile/' . $params['userId'],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        // Then
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            json_decode($client->getResponse()->getContent(), true)['errors'],
            'Internal server error trying to get user. Invalid userId: ' . $params['userId']
        );
    }

    public function testGetUserByIdWhenUserIdDoesNotExistShouldBeError()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzc3NjM3NjMsImV4cCI6MTY3Nzc5MjU2Mywicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiJdLCJ1c2VybmFtZSI6ImRldkBkZXYuY29tIiwiY29tcGFueUlkIjoiNmFjY2I4YzMtMmZmNC00N2UzLT' .
            'lkNTMtZDFlNTY2YTI4OTg4IiwiY29tcGFueU5hbWUiOiJEZXYgMSIsInZybiI6dHJ1ZX0.eeI1ni-CdvNcMZ2pOjcNOy0I6tdT' .
            'f-UqUZIcgtY7izyKwBS-I5Aela8YurBkgwGQ0ryocIrPUfAde_D7brzNuxKrcZs16IXt8ZPqw-3-y-OSqlI64BJO5X_Qwf88oH' .
            'd1ZqxHx8Fy5DCdB0bg9oK8HvSPk66YCcOgw2WMZIhVew2equ7Wnj6ts_LXvmLRUYCIqXi6TZkTqE9_CTbciI-fZvWCVts1-dTM' .
            'vdBgq0ReHZFiapXQr7NiouP3XZeUeoCE0XRqitC6KFEB_mMbwWkm4wVuKghemEjPfl9Sw5t2xdCVx9pzGIHDXRItm1AsFJdfXe' .
            '9NzxxW791YcmQ8il9BEg';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        $params = [
            'userId' => '6b16ccdf-acb7-4b40-967b-f8cae44008c8',
        ];

        // When
        $client->request(
            'GET',
            '/api/v1/user/profile/' . $params['userId'],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        // Then
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            json_decode($client->getResponse()->getContent(), true)['errors'],
            'Exception error trying to get user. User not found by ID: ' . $params['userId']
        );
    }
}
