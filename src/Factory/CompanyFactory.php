<?php

namespace App\Factory;

use App\Entity\Company;
use App\Exception\InvalidFieldsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CompanyFactory
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function createFromArray(array $data): Company
    {
        $company = new Company();

        return $this->updateFromArray($company, $data);
    }

    public function updateFromArray(Company $company, array $data): Company
    {
        if (isset($data['name'])) {
            $company->setName($data['name']);
        }
        if (isset($data['vat'])) {
            $company->setVat($data['vat']);
        }
        if (isset($data['address'])) {
            $company->setAddress($data['address']);
        }
        if (isset($data['city'])) {
            $company->setCity($data['city']);
        }
        if (isset($data['postCode'])) {
            $company->setPostCode($data['postCode']);
        }

        $violations = $this->validator->validate($company);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            throw new InvalidFieldsException($errors);
        }

        return $company;
    }
}
