<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class OrderDate extends Constraint
{
    public $message = 'Le créneau horaire {{ value }} est déjà complet.';

    public function validatedByDate()
    {
        return OrderDateValidator::class;
    }
}
