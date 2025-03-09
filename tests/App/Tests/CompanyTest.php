<?php

namespace App\Tests;

use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CompanyTest extends WebTestCase
{
    private KernelBrowser $client;

    private int $companyId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        /** @var Company $company */
        $company = $entityManager->getRepository(Company::class)->findOneBy(['vat' => 1234567890]);

        $this->companyId = $company->getId();
    }

    public function testGetCompanies(): void
    {
        $this->client->request(
            'GET',
            '/api/companies?limit=2',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(200);

        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(2, count($responseData['data']));
    }

    public function testCreateCompany(): void
    {
        $this->client->request(
            'POST',
            '/api/companies',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Company One',
                'vat' => 1234567892,
                'address' => 'Address One',
                'city' => 'City',
                'postCode' => '00-000',
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(201);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $data);
        $this->assertSame('Company created successfully', $data['message']);
    }

    public function testUpdateCompany(): void
    {
        $this->client->request(
            'PUT',
            \sprintf('/api/companies/%d', $this->companyId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Company One',
                'postCode' => '00-100',
            ])
        );

        $response = $this->client->getResponse();

        $company = [
            'id' => $this->companyId,
            'name' => 'Company One',
            'vat' => '1234567890',
            'address' => 'Address One',
            'city' => 'City',
            'postCode' => '00-100',
        ];

        $this->assertResponseStatusCodeSame(200);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $data);
        $this->assertSame('Company updated successfully', $data['message']);
        $this->assertSame($company, $data['company']);
    }

    public function testGetCompany(): void
    {
        $this->client->request(
            'GET',
            \sprintf('/api/companies/%d', $this->companyId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(200);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertSame($this->companyId, $data['company']['id']);
        $this->assertArrayHasKey('employees', $data['company']);
    }

    public function testCreateCompanyWithExistingVat(): void
    {
        $this->client->request(
            'POST',
            '/api/companies',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Company One',
                'vat' => '1234567890',
                'address' => 'Address One',
                'city' => 'City',
                'postCode' => '00-000',
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('error', $data);
        $this->assertSame('Invalid fields', $data['error']);
        $this->assertArrayHasKey('fields', $data);
        $this->assertArrayHasKey('vat', $data['fields']);
    }

    public function testCreateCompanyWithInvalidFields(): void
    {
        $this->client->request(
            'POST',
            '/api/companies',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'address' => 'Address One',
                'city' => 'City',
                'postCode' => '00-0',
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('error', $data);
        $this->assertSame('Invalid fields', $data['error']);
        $this->assertArrayHasKey('fields', $data);
        $this->assertArrayHasKey('vat', $data['fields']);
        $this->assertArrayHasKey('name', $data['fields']);
        $this->assertArrayHasKey('postCode', $data['fields']);
    }
}
