<?php

namespace App\Form\Main\Funds;

use App\Entity\Main\Funds\DebtRenewal;
use App\Form\AppAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DeleteDebtRenewalType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DebtRenewal::class,
        ]);
    }
}
