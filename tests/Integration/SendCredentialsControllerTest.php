<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SendCredentialsControllerTest extends WebTestCase
{
    public function testSendCredentialsWhenIsSuccess()
    {
        $client = static::createClient();

        $payload = [
            'reason' => 'NEW_CREDENTIALS',
            'username' => 'john.doe@example.co.tz',
            'email' => 'john.doe@example.co.tz',
            'company' => 'Simplify'
        ];

        $client->request(
            'POST',
            '/api/v1/credentials/send',
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
}
