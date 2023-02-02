<?php

namespace App\Form\Assistances\Utils;

use App\Entity\Assistances\Assistance;
use App\Form\AppAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssistanceDeleteType extends AppAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Assistance::class,
        ]);
    }
}
