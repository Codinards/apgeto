<?php

namespace App\Form\Validators;

use App\Exception\AppException;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UpperThanValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UpperThan) {
            throw new NotFoundHttpException();
        }

        $field = $constraint->field;
        dd($this->context);
        if (is_null($field)) {
            throw new InvalidArgumentException();
        }
    }
}
