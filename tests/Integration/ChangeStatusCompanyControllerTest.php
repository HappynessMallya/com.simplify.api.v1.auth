<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChangeStatusCompanyControllerTest extends WebTestCase
{
    public function testChangeStatusOfCompanyToBlockedWhenStatusIsActiveShouldBeSuccess()
    {
        $client = static::createClient();

        /** @var CompanyRepository $companyRepository */
        $companyRepository = $client->getContainer()->get(CompanyRepository::class);

        $tin = '126140304';
        $payload = [
            'status' => 'BLOCK'
        ];

        $client->request(
            'PUT',
            '/api/v1/company/changeStatus/' . $tin,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($payload)
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(json_decode($client->getResponse()->getContent(), true)['success']);

        $company = $companyRepository->findOneBy(['tin' => $tin]);

        $this->assertNotNull($company);
        $this->assertEquals(CompanyStatus::STATUS_BLOCK()->getValue(), $company->companyStatus());
    }

    public function testChangeStatusOfCompanyToActiveWhenStatusIsBlockShouldBeSuccess()
    {
        $client = static::createClient();

        /** @var CompanyRepository $companyRepository */
        $companyRepository = $client->getContainer()->get(CompanyRepository::class);

        $tin = '126140304';
        $payload = [
            'status' => 'ACTIVE'
        ];

        $client->request(
            'PUT',
            '/api/v1/company/changeStatus/' . $tin,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($payload)
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(json_decode($client->getResponse()->getContent(), true)['success']);

        $company = $companyRepository->findOneBy(['tin' => $tin]);

        $this->assertNotNull($company);
        $this->assertEquals(CompanyStatus::STATUS_ACTIVE()->getValue(), $company->companyStatus());
    }

    public function testChangeStatusOfCompanyToBlockWhenStatusIsBlockShouldBeSuccess()
    {
        $client = static::createClient();

        $expectedMessage = 'The company already has the same status';

        /** @var CompanyRepository $companyRepository */
        $companyRepository = $client->getContainer()->get(CompanyRepository::class);

        $tin = '126140304';
        $payload = [
            'status' => 'BLOCK'
        ];

        $company = $companyRepository->findOneBy(['tin' => $tin]);

        $company->updateCompanyStatus(CompanyStatus::STATUS_BLOCK());
        $companyRepository->save($company);

        $client->request(
            'PUT',
            '/api/v1/company/changeStatus/' . $tin,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($payload)
        );

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertEquals($expectedMessage, $client->getResponse()->getContent());
    }

    public function testChangeStatusOfCompanyWhenStatusIsInvalid()
    {
        $client = static::createClient();

        $expectedMessage = 'The value you selected is not a valid choice.';

        /** @var CompanyRepository $companyRepository */
        $companyRepository = $client->getContainer()->get(CompanyRepository::class);

        $tin = '126140304';
        $payload = [
            'status' => 'ANYTHING'
        ];

        $client->request(
            'PUT',
            '/api/v1/company/changeStatus/' . $tin,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($payload)
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals($expectedMessage, json_decode($client->getResponse()->getContent(), true)['errors']['status']);
    }
}
