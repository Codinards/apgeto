<?php

namespace App\Form\Utils;

use App\Entity\Tontines\Unity;
use App\Entity\Utils\UnityCotisation;
use App\Form\AppAbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnityCotisationType extends AppAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isChecked', CheckboxType::class, [
                'required' => false,
                'label' => isset($options['attr']['count']) ? $options['attr']['count'] .  ' cotisation' : 'cotisation'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UnityCotisation::class,
            ]
        );
    }
}
