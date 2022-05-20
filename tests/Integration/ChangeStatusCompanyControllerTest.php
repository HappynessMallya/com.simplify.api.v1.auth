<?php

declare(strict_types=1);

namespace App\Tests\Integration;

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
            'http://localhost:1001/api/v1/company/changeStatus/' . $tin,
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
        $this->assertEquals(CompanyStatus::STATUS_BLOCK(), $company->status());
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
            'http://localhost:1001/api/v1/company/changeStatus/' . $tin,
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
        $this->assertEquals(CompanyStatus::STATUS_ACTIVE(), $company->status());
    }
}
