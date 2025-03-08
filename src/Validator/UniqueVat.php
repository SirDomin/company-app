<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueVat extends Constraint
{
    public string $message = 'The VAT number "{{ value }}" is already in use.';
}
