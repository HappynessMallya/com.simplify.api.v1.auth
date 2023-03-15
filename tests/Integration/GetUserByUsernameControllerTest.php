<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GetUserByUsernameControllerTest
 * @package App\Tests\Integration\Controller
 */
class GetUserByUsernameControllerTest extends WebTestCase
{
    public function testSubmitValidDataShouldBeSuccess()
    {
        // Given
        $client = self::createClient();

        // Must be changed every so often
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg4ODMxODAsImV4cCI6MTY3ODkxMTk4MCwicm9sZXMi' .
            'OlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiam9obi5kb2VAZXhhbXBsZS5jby50eiIsImNvbXBhbnlJZCI6ImRhODEyMmM2LT' .
            'Q1YjEtNGUyYS05Y2EwLTk1NmVkMjJjYmU4YyJ9.Md6Zq-90Hm_JQDICerO3Gmy88b_fdljr7-mugKwA4GdT3tvGh2VAIdGg0qS' .
            'QjNzC3GcalQmI65WyAs64WuSayd8Qjj6CypyKb-Vp-Mh7YY25jFPxXVtkNIpLVsvjKBqaCF4vyciKQZz5UjmXAH1NUQOGmoTtG' .
            '4TuU6zKB7zAC6zgh6Get-VN7Z7DYZ7UsDVkZKu-GsEOPmINexVYzU_31KRmKW96-I-_N3cL1C6R8EIgX_Q5BObQyVJRy6OzkMR' .
            'HzEyxYO3DCEm6Apsn3QO18ctXe0y2ecbF577Ob-oEKsNH2Rx_W9iAV3X-o1wIjV4xOZdxPsAXj5j19ggRSgQ3yA';

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        // When
        $client->request(
            'GET',
            '/api/v1/user/profile',
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
}
