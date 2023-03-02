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
    public function testGetUserByIdWhenIsSuccess()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzc2ODU5OTQsImV4cCI6MTY3NzcxNDc5NCwicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiJdLCJ1c2VybmFtZSI6ImRldkBkZXYuY29tIiwiY29tcGFueUlkIjoiNmFjY2I4YzMtMmZmNC00N2UzLT' .
            'lkNTMtZDFlNTY2YTI4OTg4IiwiY29tcGFueU5hbWUiOiJEZXYgMSIsInZybiI6dHJ1ZX0.eE7lO9zvaCubDjURW4nK2FAI6xCt' .
            '09Og2NxnNEcBAF29WBEzD8tkg31H5CiIi3JEFj_HDlUhNIoKw0j7AVF1bWgj-709R_xuBrRwZ721-H8KPM1Eof5dqcH6RZREtD' .
            'dhOFCLal-OhSTCvP9DfqwiHIQr9UXl33uVGkRTjKtkudn2w90ge7FjZKrAeFMZDIebjIVdifi9FDxJsHCDIrN2jzDxUmp1ndsD' .
            'Kf2EXO2RYkqJMtNHEWK788Lp4_Sdd-D2n6kedaJa_mm71o6Q9ieysE8lda3c2zhjEDXLZ_DRtqStuALEsAneGeCh_dT-fsXFJP' .
            'Q-HTErFdRc9j79ojK9FQ';

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
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzc2ODU5OTQsImV4cCI6MTY3NzcxNDc5NCwicm9sZXMi' .
            'OlsiUk9MRV9BRE1JTiJdLCJ1c2VybmFtZSI6ImRldkBkZXYuY29tIiwiY29tcGFueUlkIjoiNmFjY2I4YzMtMmZmNC00N2UzLT' .
            'lkNTMtZDFlNTY2YTI4OTg4IiwiY29tcGFueU5hbWUiOiJEZXYgMSIsInZybiI6dHJ1ZX0.eE7lO9zvaCubDjURW4nK2FAI6xCt' .
            '09Og2NxnNEcBAF29WBEzD8tkg31H5CiIi3JEFj_HDlUhNIoKw0j7AVF1bWgj-709R_xuBrRwZ721-H8KPM1Eof5dqcH6RZREtD' .
            'dhOFCLal-OhSTCvP9DfqwiHIQr9UXl33uVGkRTjKtkudn2w90ge7FjZKrAeFMZDIebjIVdifi9FDxJsHCDIrN2jzDxUmp1ndsD' .
            'Kf2EXO2RYkqJMtNHEWK788Lp4_Sdd-D2n6kedaJa_mm71o6Q9ieysE8lda3c2zhjEDXLZ_DRtqStuALEsAneGeCh_dT-fsXFJP' .
            'Q-HTErFdRc9j79ojK9FQ';

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
    }
}
