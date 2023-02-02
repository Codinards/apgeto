<?php

namespace App\Form\Utils;

use App\Entity\Utils\DataCotisation;
use App\Entity\Utils\UnityCotisation;
use App\Form\AppAbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataCotisationType extends AppAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unities', CollectionType::class, [
                'entry_type' => UnityCotisationType::class,
                'label' => false,
            ]);
    }

    public function resolveData($items)
    {
        if ($this->data === null) {
            $this->data = $items;
        }

        return $this->data;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => DataCotisation::class,
                'data_options' => null,
            ]
        );
    }
}
