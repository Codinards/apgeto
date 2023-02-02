<?php

namespace App\Form\Validators;

use App\Exception\AppException;
use InvalidArgumentException;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\DependencyInjection\Exception\InvalidParameterTypeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LowerThanValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof LowerThan) {
            throw new NotFoundHttpException();
        }

        $field = $constraint->field;
        $data = $this->context->getObject();
        $target = $this->context->getPropertyName();

        if (is_null($field)) {
            throw new InvalidArgumentException(
                'Argument ' . get_class($constraint) . '::' . 'field missing in the annotations of ' . get_class($data) . '::' . $target . ' property'
            );
        }


        if (!method_exists($data, ($method = 'get' . ucfirst($field)))) {
            throw new InvalidArgumentException('Method ' . get_class($data) . '::' . $method . '() does not exists');
        }
        $targetValue = $data->$method();
        if (!is_int($targetValue)) {
            throw new InvalidTypeException();
        }
        if (!is_int($value)) {
            throw new InvalidArgumentException();
        }

        if ($targetValue < $value) {
            $this->context->buildViolation(
                'This field must be least than ' . $targetValue
            )->addViolation();
        }
    }
}
