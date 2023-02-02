<?php

namespace App\Form\Utils;

use App\Entity\Utils\BaseCotisation;
use App\Form\AppAbstractType;
use App\Form\Tontines\CotisationDayType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseCotisationType extends AppAbstractType
{

    private $data;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', CotisationDayType::class, [
                'label' => false
            ])
            ->add('items', CollectionType::class, [
                'entry_type' => TontineCotisationType::class,
                'label' => false,
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => BaseCotisation::class,
            ]
        );
    }
}
