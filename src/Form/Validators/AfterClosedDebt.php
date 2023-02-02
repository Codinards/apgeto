<?php

namespace App\Form\Validators;

use App\Exception\AppException;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class AfterClosedDebt extends Constraint
{

    public function __construct(array $options = [])
    {

        parent::__construct($options);
    }

    public function validatedBy()
    {
        return static::class . 'Validator';
    }
}
