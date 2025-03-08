<?php

namespace App\Tests\App\Tests;

use App\Entity\Company;
use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmployeeTest extends WebTestCase
{
    private KernelBrowser $client;

    private int $employeeId;

    private int $companyId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        /** @var Employee $employee */
        $employee = $entityManager->getRepository(Employee::class)->findOneBy([]);

        $this->employeeId = $employee->getId();

        /** @var Company $company */
        $company = $entityManager->getRepository(Company::class)->findOneBy([]);

        $this->companyId = $company->getId();
    }

    public function testGetEmployees(): void
    {
        $this->client->request(
            'GET',
            \sprintf('/api/companies/%d/employees', $this->companyId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(200);

        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(1, $responseData['total']);
        $this->assertSame(1, count($responseData['data']));
    }

    public function testCreateEmployee(): void
    {
        $this->client->request(
            'POST',
            '/api/employees',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstName' => 'Grzegorz',
                'lastName' => 'Brzęczyszczykiewicz',
                'email' => 'grzegorz.b@firma.pl',
                'company' => $this->companyId,
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(201);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $data);
        $this->assertSame('Employee created successfully', $data['message']);

        $this->assertArrayHasKey('company', $data['employee']);
    }

    public function testUpdateEmployee(): void
    {
        $this->client->request(
            'PUT',
            \sprintf('/api/employees/%d', $this->employeeId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstName' => 'Pan',
                'lastName' => 'Tadeusz',
            ])
        );

        $response = $this->client->getResponse();

        $company = [
            'id' => $this->employeeId,
            'firstName' => 'Pan',
            'lastName' => 'Tadeusz',
            'email' => 'jan.kowalski@company.pl',
            'phoneNumber' => '+48 123 123 123',
            'company' => [
                'id' => $this->companyId,
                'name' => 'Company One',
                'vat' => '1234567890',
                'address' => 'Address One',
                'city' => 'City',
                'postCode' => '00-000',
            ],
        ];

        $this->assertResponseStatusCodeSame(200);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $data);
        $this->assertSame('Employee updated successfully', $data['message']);
        $this->assertSame($company, $data['employee']);
    }

    public function testGetEmployee(): void
    {
        $this->client->request(
            'GET',
            \sprintf('/api/employees/%d', $this->employeeId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(200);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertSame($this->employeeId, $data['employee']['id']);
        $this->assertArrayHasKey('company', $data['employee']);
    }

    public function testCreateEmployeeWithInvalidFields(): void
    {
        $this->client->request(
            'POST',
            '/api/employees',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstName' => '',
                'lastName' => 'Test',
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);

        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('error', $data);
        $this->assertSame('Invalid fields', $data['error']);
        $this->assertArrayHasKey('fields', $data);
        $this->assertArrayHasKey('firstName', $data['fields']);
        $this->assertArrayHasKey('email', $data['fields']);
        $this->assertArrayHasKey('company', $data['fields']);
    }
}
