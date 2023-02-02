<?php

namespace App\Form\Validators;

use App\Exception\AppException;
use Attribute;
use DateTimeInterface;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class NotBeforeDate extends Constraint
{
    public ?DateTimeInterface $value;

    public $message;

    public function __construct(array $options = [])
    {

        $this->value = $options['value'] ?? null;
        $this->message = $options['message'] ?? null;
        // if ($this->value === null) {
        //     throw new InvalidArgumentException('Missing "value" options in ' . __METHOD__ . ' options arguments');
        // }
        parent::__construct($options);
    }

    public function validatedBy()
    {
        return static::class . 'Validator';
    }
}
