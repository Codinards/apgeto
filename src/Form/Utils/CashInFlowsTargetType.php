<?php

namespace App\Form\Utils;

use App\Entity\Main\Users\User;
use App\Entity\Utils\CashInFlowTarget;
use App\Form\AppAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CashInFlowsTargetType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CashInFlowTarget::class,
        ]);
    }
}
