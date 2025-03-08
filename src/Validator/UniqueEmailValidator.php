<?php

namespace App\Validator;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid constraint', get_class($constraint)));
        }

        if (null === $value || '' === $value) {
            return;
        }

        $existingEmployee = $this->entityManager->getRepository(Employee::class)->findOneBy(['email' => $value]);

        /** @var Employee|null $currentEmployee */
        $currentEmployee = $this->context->getObject();

        if ($existingEmployee && $currentEmployee instanceof Employee && $existingEmployee->getId() !== $currentEmployee->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
