<?php

namespace App\Form\Tontines;

use App\Entity\Tontines\Unity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('achat')
            ->add('amount')
            ->add('createdAt')
            ->add('benefitAt')
            ->add('isWon')
            ->add('isStopped')
            ->add('stoppedAt')
            ->add('isDemiNom')
            ->add('tontineur')
            ->add('tontine')
            ->add('avaliste')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Unity::class,
        ]);
    }
}
