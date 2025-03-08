<?php

namespace App\DataFixtures;

use App\Factory\CompanyFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{
    public function __construct(
        private readonly CompanyFactory $companyFactory,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $companyData = [
            ['name' => 'Company One', 'vat' => 1234567890, 'address' => 'Address One', 'city' => 'City', 'postCode' => '00-000'],
            ['name' => 'Company Two', 'vat' => 1234567891, 'address' => 'Address Two', 'city' => 'City 2', 'postCode' => '00-001'],
        ];

        foreach ($companyData as $key => $data) {
            $company = $this->companyFactory->createFromArray($data);

            $manager->persist($company);

            $this->addReference(\sprintf('company_%s', $key + 1), $company);
        }

        $manager->flush();
    }
}
