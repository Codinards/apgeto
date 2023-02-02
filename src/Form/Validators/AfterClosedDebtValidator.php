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


class AfterClosedDebtValidator extends ConstraintValidator
{
    public function __construct(
        private DebtRepository $debtRepository,
        private DateFormatterExtension $dateFormatterExtension
    ) {
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof AfterClosedDebt) {
            throw new NotFoundHttpException();
        }

        if (!$value instanceof DateTime) {
            throw new NotFoundHttpException();
        }

        $entity = $this->context->getObject()->getParent()->getData();
        if (!$entity instanceof LoanDataUpdate) {
            throw new InvalidDirectory('form parent data is not instance of ' . LoanDataUpdate::class);
        }
        $debt = $entity->getDebt();
        if ($debt->getParent() !== null and $value < $parentCreatedAt = ($debt->getParent()->getCreatedAt())) {
            $this->context->buildViolation(
                'Cette valeur('
                    . $this->dateFormatterExtension->dateInLocale($value)
                    . ') ne doit pas être antérieure à la date('
                    . $this->dateFormatterExtension->dateInLocale($parentCreatedAt)
                    . ') initiale du prêt'
            )->addViolation();
            return;
        }

        /** @var Debt $lastClosedLoan */
        $lastClosedLoan = $this->debtRepository->findLast(
            [
                "notin" => ['field' => 'parent', 'values' => [($debt->getParent() ?? $debt)->getId()]],
                'user' => $debt->getUser()
            ]
        );

        if ($lastClosedLoan !== null and $lastClosedLoan->getCreatedAt() > $value) {
            $this->context->buildViolation(
                'Cette valeur('
                    . $this->dateFormatterExtension->dateInLocale($value)
                    . ') ne doit pas être antérieure à la date('
                    . $this->dateFormatterExtension->dateInLocale($lastClosedLoan->getCreatedAt())
                    . ') d\'avant remboursement d\'un prêt anterieur'
            )->addViolation();
            return;
        }

        foreach ($debt->getChildren() as $child) {
            if ($child->getCreatedAt() < $value) {
                $this->context->buildViolation(
                    'Cette valeur('
                        . $this->dateFormatterExtension->dateInLocale($value)
                        . ') ne doit pas être postérieure à la date('
                        . $this->dateFormatterExtension->dateInLocale($child->getCreatedAt())
                        . ') du début du remboursement de ce prêt'
                )->addViolation();
                return;
            }
        }
    }
}
