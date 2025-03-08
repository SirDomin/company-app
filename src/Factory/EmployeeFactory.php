<?php

namespace App\Factory;

use App\Entity\Employee;
use App\Exception\InvalidFieldsException;
use App\Repository\CompanyRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmployeeFactory
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CompanyRepository $companyRepository,
    ) {
    }

    public function createFromArray(array $data): Employee
    {
        $company = new Employee();

        return $this->updateFromArray($company, $data);
    }

    public function updateFromArray(Employee $employee, array $data): Employee
    {
        if (isset($data['firstName'])) {
            $employee->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $employee->setLastName($data['lastName']);
        }
        if (isset($data['email'])) {
            $employee->setEmail($data['email']);
        }
        if (isset($data['phoneNumber'])) {
            $employee->setPhoneNumber($data['phoneNumber']);
        }

        $errors = [];

        if (isset($data['company'])) {
            $company = $this->companyRepository->find($data['company']);

            if (null !== $company) {
                $company->addEmployee($employee);
            } else {
                $errors['company'] = \sprintf('Company with id %d does not exist', (int) $data['company']);
            }
        }

        $violations = $this->validator->validate($employee);

        if (count($violations) > 0 || count($errors) > 0) {
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            throw new InvalidFieldsException($errors);
        }

        return $employee;
    }
}
