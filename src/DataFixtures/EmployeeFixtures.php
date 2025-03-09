<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Factory\EmployeeFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EmployeeFixtures extends Fixture
{
    public function __construct(
        private readonly EmployeeFactory $employeeFactory,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $employeeData = [
            ['firstName' => 'Jan', 'lastName' => 'Kowalski', 'email' => 'jan.kowalski@company.pl', 'phoneNumber' => '+48 123 123 123', 'company_ref' => 'company_1'],
        ];

        for ($i = 1; $i <= 150; $i++) {
            $employeeData[] = [
                'firstName' => "Employee$i",
                'lastName' => "Lastname$i",
                'email' => "employee$i@company.pl",
                'phoneNumber' => "+48 123 123 " . str_pad($i, 3, '0', STR_PAD_LEFT),
                'company_ref' => "company_3",
            ];
        }

        for ($i = 1; $i <= 60; $i++) {
            $companyId = rand(4, 14);

            $employeeData[] = [
                'firstName' => "Employee$i",
                'lastName' => "Lastname$i",
                'email' => "employee$i@company$companyId.pl",
                'phoneNumber' => "+48 123 456 " . str_pad($i, 3, '0', STR_PAD_LEFT), // Unique phone number
                'company_ref' => "company_$companyId",
            ];
        }


        foreach ($employeeData as $data) {
            $company = $this->getReference($data['company_ref'], Company::class);

            $data['company'] = $company->getId();
            $employee = $this->employeeFactory->createFromArray($data);

            $manager->persist($employee);
        }

        $manager->flush();
    }
}
