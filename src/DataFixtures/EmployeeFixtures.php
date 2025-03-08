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

        foreach ($employeeData as $data) {
            $company = $this->getReference($data['company_ref'], Company::class);

            $data['company'] = $company->getId();
            $employee = $this->employeeFactory->createFromArray($data);

            $manager->persist($employee);
        }

        $manager->flush();
    }
}
