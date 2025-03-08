<?php

namespace App\Exception;

class InvalidFieldsException extends \Exception
{
    private $invalidFields;

    public function __construct(array $invalidFields, $message = 'Invalid fields provided', $code = 400)
    {
        parent::__construct($message, $code);
        $this->invalidFields = $invalidFields;
    }

    public function getInvalidFields()
    {
        return $this->invalidFields;
    }
}
