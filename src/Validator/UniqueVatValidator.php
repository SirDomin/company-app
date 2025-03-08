<?php

namespace App\Validator;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueVatValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueVat) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid constraint', get_class($constraint)));
        }

        if (null === $value || '' === $value || strlen($value) > 10) {
            return;
        }

        $existingCompany = $this->entityManager->getRepository(Company::class)->findOneBy(['vat' => $value]);

        /** @var Company|null $currentCompany */
        $currentCompany = $this->context->getObject();

        dd($existingCompany);

        if ($existingCompany && $currentCompany instanceof Company && $existingCompany->getId() !== $currentCompany->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
