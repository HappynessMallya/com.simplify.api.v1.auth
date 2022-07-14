<?php

namespace App\Tests\Integration;

use App\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class VerifyUserExistsControllerTest extends WebTestCase
{
    public function testVerifyUserExistsWhenIsSuccess()
    {
        $client = static::createClient();

        $payload = [
            'username' => 'admin@simplify.co.tz'
        ];

        $client->request(
            'POST',
            '/api/v1/user/verify',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($payload)
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertTrue(json_decode($client->getResponse()->getContent(), true)['success']);
    }

    public function testVerifyUserExistsWhenIsNotExist()
    {
        $client = static::createClient();

        $payload = [
            'username' => 'admin@admin.com'
        ];

        $client->request(
            'POST',
            '/api/v1/user/verify',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($payload)
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $this->assertFalse(json_decode($client->getResponse()->getContent(), true)['success']);
    }

}
