<?php

namespace App\Form\Validators;

use App\Exception\AppException;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class UpperThan extends Constraint
{
    public $field;

    public function __construct(array $options)
    {
        $this->field = $options['field'] ?? null;

        parent::__construct($options);
    }

    public function validatedBy()
    {
        return static::class . 'Validator';
    }
}
