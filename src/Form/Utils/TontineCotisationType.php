<?php

namespace App\Form\Utils;

use App\Entity\Utils\TontineCotisation;
use App\Form\AppAbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TontineCotisationType extends AppAbstractType
{

    private $data;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('data', CollectionType::class, [
                'entry_type' => DataCotisationType::class,
                'label' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => TontineCotisation::class,
            ]
        );
    }
}
