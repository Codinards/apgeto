<?php

namespace App\Form\Main\Funds;

use App\Form\AppAbstractType;
use App\Entity\Main\Funds\Fund;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteFundType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fund::class,
        ]);
    }
}
