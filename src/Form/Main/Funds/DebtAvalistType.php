<?php

namespace App\Form\Main\Funds;

use App\Entity\Main\Funds\DebtAvalist;
use App\Entity\Main\Users\User;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DebtAvalistType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, $this->optionsMerge('avaliste', [
                'class' => User::class
            ]))
            ->add('observation', TextType::class, $this->optionsMerge('observation', [
                'required' => false
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DebtAvalist::class,
        ]);
    }
}
