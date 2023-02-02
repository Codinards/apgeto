<?php

namespace App\Form\Validators;

use App\Entity\Main\Funds\Debt;
use App\Entity\Utils\LoanDataUpdate;
use App\Repository\Main\Funds\DebtRepository;
use App\Twig\DateFormatterExtension;
use DateTime;
use Doctrine\Migrations\Finder\Exception\InvalidDirectory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class NotBeforeDateValidator extends ConstraintValidator
{
    public function __construct(
        private DebtRepository $debtRepository,
        private DateFormatterExtension $dateFormatterExtension
    ) {
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NotBeforeDate) {
            throw new NotFoundHttpException();
        }

        if (!$value instanceof DateTime) {
            throw new NotFoundHttpException();
        }

        if ($value !== null and $value <= $constraint->value) {
            $message = $constraint->message ? str_replace(
                ['{{ value }}', '{{value}}'],
                $this->dateFormatterExtension->dateInLocale($value),
                str_replace(['{{ ref }}', '{{ref}}'], $this->dateFormatterExtension->dateInLocale($constraint->value), $constraint->message)
            ) : 'Cette valeur('
                . $this->dateFormatterExtension->dateInLocale($value)
                . ') ne doit pas être antérieure à cette date('
                . $this->dateFormatterExtension->dateInLocale($constraint->value)
                . ')';
            $this->context->buildViolation($message)->addViolation();
            return;
        }
    }
}
