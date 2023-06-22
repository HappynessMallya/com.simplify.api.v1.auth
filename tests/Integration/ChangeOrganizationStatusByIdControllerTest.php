<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Model\Organization\Organization;
use App\Domain\Model\Organization\OrganizationId;
use App\Domain\Model\Organization\OrganizationStatus;
use App\Domain\Repository\OrganizationRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ChangeOrganizationStatusByIdControllerTest
 * @package App\Tests\Integration
 */
class ChangeOrganizationStatusByIdControllerTest extends WebTestCase
{
    public function testChangeOrganizationStatusByIdToInactiveWhenStatusIsActiveShouldBeSuccess()
    {
        $client = static::createClient();

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

        /** @var OrganizationRepository $companyRepository */
        $organizationRepository = $client->getContainer()->get(OrganizationRepository::class);

        $organizationId = '71e64a9d-8ac6-4dab-bee7-99bfbf4e190a';

        $client->request(
            'PUT',
            '/api/v2/organization/' . $organizationId . '?newStatus=INACTIVE',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(json_decode($client->getResponse()->getContent(), true)['success']);

        /** @var Organization $organization */
        $organization = $organizationRepository->get(OrganizationId::fromString($organizationId));

        $this->assertNotNull($organization);
        $this->assertEquals(OrganizationStatus::INACTIVE()->getValue(), $organization->getStatus());
    }

    public function testChangeOrganizationStatusByIdToInactiveWhenStatusIsInactiveShouldBeError()
    {
        $client = static::createClient();

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

        $organizationId = '71e64a9d-8ac6-4dab-bee7-99bfbf4e190a';

        $expectedMessage = 'Exception error trying to change organization status. '
            . 'Organization status is the same one: INACTIVE';

        $client->request(
            'PUT',
            '/api/v2/organization/' . $organizationId . '?newStatus=INACTIVE',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertFalse(json_decode($client->getResponse()->getContent(), true)['success']);
        $this->assertEquals(
            $expectedMessage,
            json_decode($client->getResponse()->getContent(), true)['error']
        );
    }

    public function testChangeOrganizationStatusByIdWhenStatusIsInvalidShouldBeError()
    {
        $client = static::createClient();

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

        $organizationId = '71e64a9d-8ac6-4dab-bee7-99bfbf4e190a';

        $expectedMessage = 'Exception error trying to change organization status. '
            . 'App\\Domain\\Model\\Organization\\OrganizationStatus::ANYTHING not defined';

        $client->request(
            'PUT',
            '/api/v2/organization/' . $organizationId . '?newStatus=ANYTHING',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertFalse(json_decode($client->getResponse()->getContent(), true)['success']);
        $this->assertEquals(
            $expectedMessage,
            json_decode($client->getResponse()->getContent(), true)['error']
        );

        $this->fixAll($client, $organizationId);
    }

    /**
     * @param $client
     * @param $organizationId
     */
    private function fixAll($client, $organizationId)
    {
        $client->request(
            'PUT',
            '/api/v2/organization/' . $organizationId . '?newStatus=ACTIVE',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );
    }
}
