<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UpdateCompanyControllerTest
 * @package App\Tests\Integration
 */
class UpdateCompanyControllerTest extends WebTestCase
{
    public function testSubmitValidDataShouldBeSuccess(): void
    {
        // Given
        $client = self::createClient();

        $params = [
            'companyId' => '6accb8c3-2ff4-47e3-9d53-d1e566a28988',
        ];

        $dto = [
            'name' => 'Simplify',
            'tin' => '123456789',
            'email' => 'cassian@simplify.co.tz',
            'phone' => '789456123',
            'enable' => 'false',
            'traRegistration' => '',
            'address' => 'Africa, Dar es Salaam',
        ];

        // When
        $client->request(
            'PUT',
            '/api/v1/company/' . $params['companyId'],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($dto)
        );

        // Then
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertTrue(json_decode($client->getResponse()->getContent(), true)['success']);
    }

    public function testSubmitInvalidEmailShouldBeError(): void
    {
        // Given
        $client = self::createClient();

        $params = [
            'companyId' => '6accb8c3-2ff4-47e3-9d53-d1e566a28988',
        ];

        $dto = [
            'name' => 'Simplify',
            'tin' => '123456789',
            'email' => 'cassian_simplify.co.tz',
            'phone' => '789456123',
            'enable' => 'false',
            'traRegistration' => '',
            'address' => 'Africa, Dar es Salaam',
        ];

        // When
        $client->request(
            'POST',
            '/api/v1/company/' . $params['companyId'],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($dto)
        );

        // Then
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertFalse(json_decode($client->getResponse()->getContent(), true)['success']);
    }
}
